<?php

use App\Models\Invoices;
use App\Models\OrderInvoicess;
use App\Models\Orders;
use App\User;
use TapPayments\GoSell;
use Illuminate\Support\Facades\DB;
if ( ! function_exists( 'additemtoinvoice' ) ) {
    /**
     * Get Total Refunded Amount order
     * @param $data
     *
     * @return  float|integer
     */
    function additemtoinvoice( $orderid , $invoiceId ) {
        generatexerotoken();

        $total = 0 ;

        $order = Orders::where('id',$orderid)
            ->first();
         $inveiceChk = OrderInvoicess::where('orders_id',$orderid)->first();
        if($inveiceChk)
        {
            return ;
        }
//        DB::unprepared('UNLOCK TABLES invoices WRITE');
        DB::raw('LOCK TABLES invoices WRITE');
        $lastID = Invoices::OrderBy('id','DESC')->first();
        $lastInvoiceId = 0 ;
        if($lastID)
        {
            $lastInvoiceId = $lastID->id;
        }
        $invoice = Invoices::whereId($invoiceId)->first();

            try
            {
                (voidxeroinvoice($orderid));
            }
            catch (Exception $exception){}
            $order->link_generated =1 ;
            $order->save();
            $total+= $order->amount;
             OrderInvoicess::create([
                'orders_id'=>$order->id,
                'invoices_id'=>$invoice->id
            ]);

          if($total>0)
        {
            $paymentLink =  tapmulitplepayment($total,$invoice);
            $invoice->url = $paymentLink;
            $invoice->amount = $total;
            $invoice->magic_link = generateRandomString(10);
            $invoice->update();

                $order->payment_link = route('payInvoice',$invoice->magic_link);
                $order->save();

        }
        else
        {
            $invoice = Invoices::whereId($invoice->id)->delete();
        }

        try
        {
             (xeroinvoice($invoice->id,0));
        }
        catch (\Exception $exception){}
        DB::raw('UNLOCK TABLES invoices WRITE');

    }

























}
