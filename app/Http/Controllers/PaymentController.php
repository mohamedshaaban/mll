<?php

namespace App\Http\Controllers;
use \stdClass;
use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Models\Orders;
use App\Models\PaymentTransaction;
use Backpack\NewsCRUD\app\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TapPayments\Requests\Retrieve;
use TapPayments\GoSell;
class PaymentController extends Controller
{
    public function xeropdf(Request $request)
    {
        $order = Invoices::where('magic_link',$request->magiclink)->first();
        $pdf = getxeroinvoice($order->xero_id);

        return view('payment.pdf')->with(compact('pdf'));
    }
    public function payment(Request $request)
    {
        $invoice = Invoices::where('magic_link',$request->url)->first();
        return view('payment')->with('invoice',$invoice);

    }
    public function payReturn(Request $request , $token , $status)
    {

        $returns  = $request->all();
        file_put_contents('logs/log_'.date("j.n.Y").'.log', json_encode($returns) . "\n", FILE_APPEND);

        GoSell::setPrivateKey(config('app.TAPPAYMENT_SecretAPIKey'));
        if(isset($returns['tap_id'])) {
            $returns = GoSell\Charges::retrieve($returns['tap_id']);
            file_put_contents('logs/log_' . date("j.n.Y") . '.log', json_encode($request->all()) . ' , ' . json_encode($returns) . "\n", FILE_APPEND);
        }
//dd($returns);
        if(!is_object($returns))
        {
            $returns = $this->array_to_object($returns);
        }
        $invoice_id = 0 ;
        if( $request->payment == 'invoice')
        {
            $invoice_id = $token;

            $invoice = Invoices::where('id',$token)->first();
            $orderstatus = Invoices::INVOICE_PARTIALLY_PAID ;
            if($returns->status=='CAPTURED' )
            {
                $chbpay = PaymentTransaction::where('refernece_number',$returns->reference->track)->groupBy('refernece_number')->first();
                $transaction =  PaymentTransaction::updateOrCreate(['refernece_number'=>$returns->reference->track],
                    ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                        'refernece_number'=>$returns->reference->track
                        ,'amount'=>$returns->amount
                        ,'status'=>$returns->status
                        ,'invoice_id'=>$invoice_id
                        ,'date'=>Carbon::now(),
                        'response'=>json_encode($returns)]);
                try
                {
                    if(!$chbpay)
                    {
                        (addpaymentxero( $invoice->id ,0 , $returns->amount , config('app.XEROKNET')));
                    }
                }
                catch (\Exception $exception){}

            }
            if($returns->status=='CAPTURED' ) {

                $lastTransacations = PaymentTransaction::where('invoice_id', $token)->where('status','CAPTURED')->groupBy('refernece_number')->get();
                $perviousAmount = 0 ;
                foreach ($lastTransacations as $lastTransacation)
                {
                    $perviousAmount+=$lastTransacation->amount;
                }
                if($perviousAmount == $invoice->amount)
                {
                    $orderstatus = Invoices::INVOICE_PAID ;
                }

                if($returns->status=='CAPTURED' && $orderstatus == Invoices::INVOICE_PAID ) {

                    $invoice->is_paid = 1;
                    $invoice->save();
                }
                foreach ($invoice->orders as $order )
                {
                    if($returns->status=='CAPTURED' && $orderstatus == Invoices::INVOICE_PAID) {
                        $order->is_paid = 1;
                        $order->partially_paid = Orders::Fullpaid;
                        $order->save();
                    }
                    else
                    {
                        $order->partially_paid = Orders::Partiallypaid;
                    }
                }
                $pdf = getxeroinvoice($invoice->xero_id);

                return view('payment.pdf')->with(compact('pdf'));
            }
        }
        else
        {

            $order = Orders::find($token);

            if($returns->status=='CAPTURED') {
                $order->is_paid = 1;
                $order->save();
                try
                {
                    (addpaymentxero( $order->id ,1 , $returns->amount , config('app.XEROKNET')));
                }
                catch (\Exception $exception){}
                $transaction =  PaymentTransaction::create(
                    ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                        'refernece_number'=>$returns->reference->track
                        ,'amount'=>$returns->amount
                        ,'status'=>$returns->status
//                        ,'invoice_id'=>$invoice_id
                        ,'date'=>Carbon::now(),
                        'response'=>json_encode($returns)]);
                if($returns->status=='CAPTURED' ) {
                    $pdf = getxeroinvoice($order->xero_id);
                    return view('payment.pdf')->with(compact('pdf'));
                }
                else
                {
                    $transaction =  PaymentTransaction::create(
                        ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                            'refernece_number'=>$returns->reference->track
                            ,'amount'=>$returns->amount
                            ,'status'=>$returns->status
//                        ,'invoice_id'=>$invoice_id
                            ,'date'=>Carbon::now(),
                            'response'=>json_encode($returns)]);
                    return redirect($order->payment_link)->with('alert', 'عملية الدفع لم تتم!');

                }

            }
            else
            {
                $transaction =  PaymentTransaction::create(
                    ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                        'refernece_number'=>$returns->reference->track
                        ,'amount'=>$returns->amount
                        ,'status'=>$returns->status
//                        ,'invoice_id'=>$invoice_id
                        ,'date'=>Carbon::now(),
                        'response'=>json_encode($returns)]);
                return redirect($order->payment_link)->with('alert', 'عملية الدفع لم تتم!');

            }
        }

        $transaction =  PaymentTransaction::create(
            ['order_id'=> $token, 'transaction_id'=> $returns->reference->payment,
                'refernece_number'=>$returns->reference->track
                ,'amount'=>$returns->amount
                ,'status'=>$returns->status
                ,'invoice_id'=>$invoice_id
                ,'date'=>Carbon::now(),
                'response'=>json_encode($returns)]

        );
        $pdf = getxeroinvoice($order->xero_id);
        return view('payment.pdf')->with(compact('pdf'));

    }

    function array_to_object($array) {
        $obj = new stdClass;
        foreach($array as $k => $v) {
            if(strlen($k)) {
                if(is_array($v)) {
                    $obj->{$k} = $this->array_to_object($v); //RECURSION
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        return $obj;
    }

    public function tapreturn(Request $request)
    {

    }
    public function payInvoice(Request $request)
    {


        $invoice = Invoices::where('magic_link',$request->id)->first();
        $transaction = null;
         $lastTransacations = PaymentTransaction::where('invoice_id', $invoice->id)->where('status','CAPTURED')->get();

         $perviousAmount = 0 ;
        foreach ($lastTransacations as $lastTransacation)
        {
            $perviousAmount+=$lastTransacation->amount;
        }
        if(!$invoice->is_paid)
        {
            return view('payment.index')->with(compact('invoice','transaction','perviousAmount'));
        }
        else
        {
            $pdf = getxeroinvoice($invoice->xero_id);
            // sleep for 10 seconds
            sleep(10);
            return view('payment.pdf')->with(compact('pdf'));
        }

    }
    public function payOrderInvoice(Request $request)
    {


        $invoice = Orders::where('url',$request->url)->first();
        if(!$invoice)
        {
            return ;
        }
        $transaction = null ;
        if(!$invoice->is_paid)
        {
            return view('payment.order.index')->with(compact('invoice','transaction'));

        }
        else
        {
            $pdf = getxeroinvoice($invoice->xero_id);
            return view('payment.pdf')->with(compact('pdf'));
        }

    }
    public function makePayment(Request $request)
    {
        file_put_contents('logs/log_invoice_'.date("j.n.Y").'.log', json_encode($request).\Carbon\Carbon::now() . "\n", FILE_APPEND);

        $invoice = Invoices::find($request->invoice_id);
        $data = tapmulitplepayment($request->amount,$invoice);
        return redirect($data);
    }
    public function makeOrderPayment(Request $request)
    {

        $invoice = Orders::find($request->order_id);
        (file_put_contents('logs/log_order_'.date("j.n.Y").'.log', json_encode($request).\Carbon\Carbon::now() . "\n", FILE_APPEND));

        $data = tappayment($invoice);
         return redirect($data);

    }
}