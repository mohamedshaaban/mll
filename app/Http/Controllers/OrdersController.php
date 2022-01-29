<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Models\OrderInvoicess;
use App\Models\Orders;
use App\Models\PaymentTransaction;
use Backpack\NewsCRUD\app\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TapPayments\Requests\Retrieve;
use TapPayments\GoSell;
class OrdersController extends Controller
{

    public function shareOrder(Request  $request)
    {
        \App::setLocale(session('locale'));

        $order = Orders::find($request->id);
        $text = '' ;
        $text.= trans('admin.Order Id').' : '.$order->invoice_unique_id.'%0A';
        $text.= trans('admin.Car Make').' : '.@$order->carmakes->name_en.'%0A';
        $text.= trans('admin.Car Model').' : '.@$order->carmodel->name_en.'%0A';
        $text.= trans('admin.Driver').' : '.@$order->driver->name.'%0A';
//            $text.= trans('admin.Status').' : '.@$this->requeststatus->name_en.'%0A';
//            $text.= trans('admin.Address').' : '.@$this->address.'%0A';
        $text.= trans('admin.Date').' : '.@$order->date.'%0A';
        $text.= trans('admin.Area From').' : '.@$order->areafrom->name_en.'%0A';
        $text.= trans('admin.Area To').' : '.@$order->areato->name_en.'%0A';
        $text.= trans('admin.remarks').' : '.@$order->remarks.'%0A';
        $text.= trans('admin.Amount').' : '.@$order->amount.'%0A';
 
        if($order->payment_link){$text.= trans('admin.Pay_Link').' : '.@$order->payment_link.'%0A';}

        return "https://wa.me/+965".$order->customers->mobile."/?text=".$text;
        
    }
    public function copyOrder(Request  $request)
    {
        \App::setLocale(session('locale'));

        $order = Orders::find($request->id);
        $text = '' ;
        $text.= trans('admin.Order Id').' : '.$order->invoice_unique_id.',';
        $text.= trans('admin.Car Make').' : '.@$order->carmakes->name_en.',';
        $text.= trans('admin.Car Model').' : '.@$order->carmodel->name_en.',';
        $text.= trans('admin.Driver').' : '.@$order->driver->name.',';
//            $text.= trans('admin.Status').' : '.@$this->requeststatus->name_en.',';
//            $text.= trans('admin.Address').' : '.@$this->address.',';
        $text.= trans('admin.Date').' : '.@$order->date.',';
        $text.= trans('admin.Area From').' : '.@$order->areafrom->name_en.',';
        $text.= trans('admin.Area To').' : '.@$order->areato->name_en.',';
        $text.= trans('admin.remarks').' : '.@$order->remarks.',';

        $text.= trans('admin.Amount').' : '.@$order->amount.',';

        if($order->payment_link){$text.= trans('admin.Pay_Link').' : '.@$order->payment_link.',';}


        return $text;

    }
    public function checkCustmerOrder(Request $request)
    {
        $hasOrder = 0 ;
        $order = Orders::where('is_paid',0)->where('customer_id',$request->id)->first();
        if($order) {
            $hasOrder = 1 ;
        }
        return ['hasorder'=>$hasOrder];

    }
    public function checkInvoice(Request $request)
    {
        $chkPayment = PaymentTransaction::where('invoice_id',$request->id)->first();
        if(!$chkPayment)
        {
            $invoice = Invoices::with('orders')->whereId($request->id)->first();
            foreach ($invoice->orders as $order)
            {

                $url = generateRandomString(10);
                $order->url = $url;
                $order->link_generated = 0;
                $order->payment_link = url('/') . '/payorder/' . $url;
                $order->save();
                $order->save();
                if ($order->amount == 0 || $order->amount == NULL) {
                    try {
                        $xero =   xeroquotes($order->id, 1);
                    } catch (\Exception $exception) {
                    }
                } else {
                    try {
                        $xero = xeroinvoice($order->id, 1);
                    } catch (\Exception $exception) {
                    }
                }
            }
            deletexeroinvoice($invoice->id);
            OrderInvoicess::where('invoices_id',$invoice->id)->delete();
            $invoice->delete();
            $deleted = 1 ;
            $message = 'Invoice Deleted' ;
        }
        else
        {
            $deleted= 0 ;
            $message = 'Cant Delete Invoice it has payments' ;
        }
        return ['message'=>$message,'deleted'=>$deleted];
    }
}