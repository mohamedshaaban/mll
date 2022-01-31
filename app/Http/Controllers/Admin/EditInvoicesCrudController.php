<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Request;
use App\Models\Customers;
use App\Models\Invoices;
use App\Models\OrderInvoicess;
use App\Models\Orders;
use App\Models\PaymentTransaction;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class EditInvoicesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\Invoices::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/editinvoices');
        CRUD::setEntityNameStrings(trans('admin.Generate invoices'), trans('admin.Generate invoices'));
    }

    protected function setupListOperation()
    {
        $queries = array();
        if(isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queries);

            if (isset($queries['userid']) && isset($queries['orderid'])) {
                if(sizeof($queries['orderid'])>1)
                {
                    (generateInvoice($queries));
                }
                else
                {
                    \Alert::error(trans('admin.Choose multiple orders to create invoice'))->flash();
                }

            }
        }
//        $this->crud->addColumn([ // Text
//            'name'  => 'num_of_cars',
//            'label' => '# Cars',
//            'type'      => 'text'
//        ]);
//        $this->crud->addColumn([ // Text
//            'name'  => 'name',
//            'label' => trans('admin.Name'),
//            'type'      => 'text'
//        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'mobile',
            'label' => trans('admin.Mobile'),
            'type'      => 'text'
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'num_pending_of_orders',
            'label' => trans('admin.# Pending Knet Orders'),
            'type'      => 'text'
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'amt_pending_of_orders',
            'label' => trans('admin.Amount Pending Knet Orders'),
            'type'      => 'text'
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'num_paid_of_orders',
            'label' => trans('admin.# Paid Knet Orders'),
            'type'      => 'text'
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'amt_of_orders',
            'label' => trans('admin.Amount Paid knet Orders'),
            'type'      => 'text'
        ]);        
        



        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
        $this->crud->enableDetailsRow();
        $this->crud->disableBulkActions();
        $this->crud->removeAllButtons();
    }

    protected function showDetailsRow($id)
    {
        
        $notPaidInvoices = Orders::where('paid_by',$id)->where('link_generated',0)->where('is_paid', '!=' ,1)->where('status',6)->get();
        
        $invoices = Invoices::where('customer_id',$id)->whereNotNull('magic_link')->get();
        $text='<script>$("#selectorders'.$id.'").click(function (e) {$(this).closest("table").find("td input:checkbox").prop("checked", this.checked);});</script>';
        $text.= '<div class="row"><div class="col-md-5 col-sm-12">';
        $text .= '<form acion="/admin/invoice/generate" method="get">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false"><input type="checkbox" id="selectorders'.$id.'" /></th><th data-orderable="false">'.trans('admin.Order Id').'</th><th data-orderable="false">'.trans('admin.Date').'</th><th data-orderable="false">'.trans('admin.Payment').'</th><th data-orderable="false">'.trans('admin.Amount').'</th></tr>';
        $text .='    <input type="hidden" name="userid" value="'.$id.'" />';
        foreach ($notPaidInvoices as $order)
        {

            $text.='<tr class="even">';
            $text.= '<td><input type="checkbox" name="orderid[]" class="orderchk'.$order->paid_by.'" value="'.$order->id.'"/> </td>';
            $text.= '<td><a href="/admin/orders/'.@$order->id.'/edit" target="_blank">'.@$order->invoice_unique_id.'</a></td>';
            $text.= '<td>'.@$order->date.'</td>';
//            $text.= '<td>'.@$order->areafrom->name_en.'</td>';
//            $text.= '<td>'.@$order->areato->name_en.'</td>';
            $text.= '<td>'.$order->paymenttext.'</td>';
            $text.= '<td>'.@$order->amount.'</td>';
            $text.='</tr>';
        }
        $text.='</table><input type="submit" value="'.trans('admin.Generate Invoice').'"></fom>';
        $text.='</div><div class="col-md-7 col-sm-12">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false" style="width:30%">'.trans('admin.Invoice Id').'</th><th data-orderable="false" style="width:30%">'.trans('admin.Share').'</th><th data-orderable="false" style="width:30%">'.trans('admin.Invoice Link').'</th><th width="30%" data-orderable="false">'.trans('admin.Paid').' </th><th width="30%" data-orderable="false">'.trans('admin.Amount').'</th><th data-orderable="false">'.trans('admin.Remaining').'</th><th>'.trans('admin.Edit').'</th><th width="30%" data-orderable="false">'.trans('admin.Date').'</th></tr>';
        foreach ($invoices as $invoice)
        {
            $lastTransacations = PaymentTransaction::where('invoice_id', $invoice->id)->get();

            $perviousAmount = 0 ;
            foreach ($lastTransacations as $lastTransacation)
            {
                $perviousAmount+=$lastTransacation->amount;
            }
            $text.='<tr class="even">';
            $text.= '<td><a href="/admin/editinvoices/'.$invoice->id.'/edit" class="btn btn-sm btn-link">'.@$invoice->invoice_unique_id.'</a></td>';
            $text.= '<td>'.@$invoice->share_link.'</td>';
            $text.= '<td  style="width:30%"><a href="'.route('payInvoice',$invoice->magic_link).'" target="_blank" style="max-width:30%">'.trans('admin.Pay').'</a> </td>';
            $text.= '<td>'.@$invoice->paid.'</td>';
            $text.= '<td>'.@$invoice->amount.'</td>';
            $text.= '<td>'.abs($invoice->amount - $perviousAmount).'</td>';
            $text.= '<td><a href="/admin/editinvoices/'.$invoice->id.'/edit" class="btn btn-sm btn-link"><i class="la la-edit"></i></a></td>';
            $text.= '<td>'.@Carbon::parse($invoice->created_at)->format('Y-m-d').'</td>';

            $text.='</tr>';

        }
        $text.='</table>';
        $text.='</div></div>';
        return $text;
    }
    protected function setupCreateOperation()
    {
        session(['orders' => null]);
        session(['canEdit' => true]);
        session(['invoice' =>null]);
        session(['payLink' =>null]);
        session(['shareLink' =>null]);
        session(['knetPayment' =>null]);

        $canEditFields = true;
//        $request->input('name');
        $id = (request()->route('id'));
        if($id) {
            $invoice = Invoices::with('orders')->whereId($id)->first();
            if($invoice->is_paid){$canEditFields=false;}
             if($invoice) {
                 session(['payLink' =>$invoice->magic_link]);
                 session(['shareLink' =>$invoice->share_edit_link]);

                 $checkPayments = PaymentTransaction::where('invoice_id',$id)->first();
                 $knetPayment = PaymentTransaction::where('invoice_id',$id)->where('status','CAPTURED')->get();
                  if($checkPayments)
                 {
                     session(['canEdit' => false]);
                 }
                  session(['knetPayment' => $knetPayment]);
                $orders = Orders::where('paid_by', $invoice->customer_id)
                    ->where('is_paid', Orders::ORDER_NOT_PAID)->where('status', Orders::COMPLETED_ORDER)
                    ->whereNotIn('id',OrderInvoicess::get()->pluck('orders_id')->toArray())->get();

                session(['orders' => $orders]);
                session(['invoice' => $invoice->orders]);
            }
        }
//        CRUD::setValidation(StoreRequest::class);
        session(['canEditFields' =>$canEditFields]);
        if($canEditFields){
            CRUD::addField(  // Text
            [   // view
                'name' => 'invoice_unique_id',
                'label' => trans('admin.invoice_unique_id'),
                'type' => 'text',
                'tab' => 'Texts',

            ]);

            CRUD::addField(  // Text
                [   // view
                    'name' => 'discount',
                    'label' => trans('admin.discount'),
                    'type' => 'number',
                    'tab' => 'Texts',
                ]);
            CRUD::addField(  // Text
                [   // view
                    'name' => 'custom-ajax-button',
                    'type' => 'view',
                    'view' => ('orders.custom-invoices'),
                    'tab' => 'Texts',

                ]);
            CRUD::addField([   // repeatable
                'name'  => 'payments',
                'label' => trans('admin.Payment'),
                'type'  => 'repeatable',
                'tab'   => 'Texts',

                'fields' => [
                    [
                        'name'    => 'transaction_id',
                        'type'    => 'text',
                        'label'   => trans('admin.transaction_id'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ], [
                        'name'    => 'amount',
                        'type'    => 'text',
                        'label'   => trans('admin.amount'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],[
                        'name'    => 'date',
                        'type'    => 'date',
                        'label'   => trans('admin.date'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],[
                        'name'    => 'payment_type',
                        'type'    => 'select_from_array',
                        'label'   => trans('admin.payment_type'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'options' => [
                            // the key will be stored in the db, the value will be shown as label;
                            Orders::CASH_PAYMENT => trans("admin.Cash"),
                            Orders::CHECK_PAYMENT => trans("admin.Check"),
                        ],
                    ],

                ],

                // optional
                'new_item_label'  => 'Add Payment', // customize the text of the button

            ],);
        }
        else {
            $this->crud->removeSaveActions(['save_and_edit','save_and_back','save_and_new']);
            CRUD::addField(  // Text
                [   // view
                    'name' => 'invoice_unique_id',
                    'label' => trans('admin.invoice_unique_id'),
                    'type' => 'text',
                    'tab' => 'Texts',
                    'readonly' => 'readonly',

                ]);

            CRUD::addField(  // Text
                [   // view
                    'name' => 'discount',
                    'label' => trans('admin.discount'),
                    'type' => 'number',
                    'tab' => 'Texts',
                    'readonly' => 'readonly',
                ]);
            CRUD::addField(  // Text
                [   // view
                    'name' => 'custom-ajax-button',
                    'type' => 'view',
                    'view' => ('orders.custom-invoices'),
                    'tab' => 'Texts',
                    'readonly' => 'readonly',

                ]);
            CRUD::addField([   // repeatable
                'name'  => 'payments',
                'label' => trans('admin.Payment'),
                'type'  => 'repeatable',
                'tab'   => 'Texts',
                'readonly' => 'readonly',
                'fields' => [
                    [
                        'name'    => 'transaction_id',
                        'type'    => 'text',
                        'readonly' => 'readonly',
                        'label'   => trans('admin.transaction_id'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ], [
                        'name'    => 'amount',
                        'type'    => 'text',
                        'readonly' => 'readonly',
                        'label'   => trans('admin.amount'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],[
                        'name'    => 'date',
                        'type'    => 'date',
                        'readonly' => 'readonly',
                        'label'   => trans('admin.date'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],[
                        'readonly' => 'readonly',
                        'name'    => 'payment_type',
                        'type'    => 'select_from_array',
                        'label'   => trans('admin.payment_type'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'options' => [
                            // the key will be stored in the db, the value will be shown as label;
                            Orders::CASH_PAYMENT => trans("admin.Cash"),
                            Orders::CHECK_PAYMENT => trans("admin.Check"),
                        ],
                    ],

                ],

                // optional
                'new_item_label'  => 'Add Payment', // customize the text of the button

            ],);

        }




        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }
    public function update()
    {
        //Get Total Amount of payment
        $totalPaidAmt  = 0 ;
        $totalOrderAmt  = 0 ;
        $totalAmt  = 0 ;
        if(sizeof($_REQUEST['orderId']) < 2)
        {
            return \Redirect::back()->withErrors(['# of orders must be greater than 1']);
        }
        if($_REQUEST['payments']){
        foreach(json_decode($_REQUEST['payments']) as $payment){
            if($payment->amount)
            {
                $totalAmt += $payment->amount;
            }
        }}
        //End OF Get Total Amount of payment
        //Get Total Amount of Orders
        foreach(($_REQUEST['orderId']) as $orderId){
            $order = Orders::find($orderId);
            $totalOrderAmt += $order->amount - $order->discount;

        }
        $previousInvoice = Invoices::find($_REQUEST['id']);

        //Discount Cant be greater than total orders
        $paymentsAmt = PaymentTransaction::where('invoice_id',$previousInvoice->id)->where('status','CAPTURED')->sum('amount');

        if(isset($_REQUEST['discount'])&& ($_REQUEST['discount'] > $totalOrderAmt))
        {
            return \Redirect::back()->withErrors(['Discount Cant be greater than total of orders']);
        }
        if(isset($_REQUEST['discount'])&& ($_REQUEST['discount']+$paymentsAmt>$previousInvoice->amount))
        {
            return \Redirect::back()->withErrors(['Discount Cant be greater than remaining amount']);
        }
        //Total PAyment Cant be greater than total orders
        if($totalAmt > $totalOrderAmt)
        {
            return \Redirect::back()->withErrors(['Total Payment Cant be greater than total of orders']);
        }
        //Total PAyment Cant be greater than total orders
        if($totalAmt > $totalOrderAmt-($_REQUEST['discount']+$paymentsAmt))
        {
            return \Redirect::back()->withErrors(['Total Payment Cant be greater than the remaining amount of orders']);
        }


        //End OF Get Total Amount of Orders

        $perviousOrdersIds = [] ;
        foreach($previousInvoice->orders as $order)
        {
            $perviousOrdersIds[] = $order->id;
        }


        $response = $this->traitUpdate();

        $newInvoice = Invoices::find($this->crud->entry->id);
        $diffs = (array_diff($_REQUEST['orderId'], $perviousOrdersIds));
        //Delete pervious orders from invoice & add new ones
        OrderInvoicess::where('invoices_id',$newInvoice->id)->delete();
        foreach ($_REQUEST['orderId']as $orderId)
        {
            OrderInvoicess::create(['invoices_id'=>$newInvoice->id,'orders_id'=>$orderId]);
        }
        //Delete pervious orders from invoice & add new ones
        (deletepaymentxero($newInvoice->id));

        edititemtoinvoice($newInvoice->id);
//Create Xeror order for the removed orders from invoice
        foreach ($perviousOrdersIds as $diff)
        {
            if(!in_array($diff,$_REQUEST['orderId']))
            {
                $orderold = Orders::whereId($diff)->first();
                $url = generateRandomString(10);
                $orderold->url = $url;
                $orderold->link_generated = 0;
                $orderold->status = Orders::COMPLETED_ORDER;
                $orderold->payment_link = url('/') . '/payorder/' . $url;
                $orderold->save();
                if ($orderold->amount == 0 || $orderold->amount == NULL) {
                    try {
                        $xero =   xeroquotes($orderold->id, 1);
                    } catch (\Exception $exception) {
                    }
                } else {
                    try {
                        $xero = xeroinvoice($orderold->id, 1);
                    } catch (\Exception $exception) {
                    }
                }
            }
        }

        //Add Payments
        if($_REQUEST['payments']){
            PaymentTransaction::where('invoice_id')->where('payment_type','!=','knet')->delete();
            foreach(json_decode($newInvoice->payments) as $payment){
                if($payment->amount) {
                    PaymentTransaction::create(
                        ['order_id' => $newInvoice->id, 'transaction_id' => $payment->transaction_id,
                            'refernece_number' => $payment->transaction_id
                            , 'amount' => $payment->amount
                            , 'status' => 'CAPTURED'
                            , 'invoice_id' => $newInvoice->id
                            , 'date' => Carbon::now()
                            , 'payment_type' => ($payment->payment_type == 1) ? 'cash' : 'check'
                            , 'response' => json_encode($payment)]);
                }
            }
            foreach(PaymentTransaction::where('invoice_id',$newInvoice->id)->where('status','CAPTURED')->get() as $payment){
                if($payment->amount && $payment->status=='CAPTURED')
                {
                    if($payment->payment_type == Orders::CASH_PAYMENT)
                    {
                        $acount = config('app.XEROCASH');
                    }
                    else if ($payment->payment_type == Orders::CHECK_PAYMENT)
                    {
                        $acount = config('app.XEROCHECK');
                    }
                    else
                    {
                        $acount = config('app.XEROKNET');
                    }
                    if($payment->amount)
                    {
                        (addpaymentinvoicexero( $newInvoice->id ,0 , $payment->amount , $acount,$payment->date));
                    }
                }
            }}
        $paymentsAmt = PaymentTransaction::where('invoice_id',$newInvoice->id)->where('status','CAPTURED')->sum('amount');

        if($paymentsAmt >= ($newInvoice->amount-$newInvoice->discount) || ($newInvoice->amount <= $newInvoice->discount)||($paymentsAmt+$newInvoice->discount>=$newInvoice->amount))
        {
            $newInvoice->is_paid = 1 ;
            $newInvoice->save();
        }
        return $response;

    }
    protected function setupUpdateOperation()
    {
         $this->setupCreateOperation();
    }
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $areas = Customers::where('name','like','%'.$request->q.'%')->get(['id','name']);
        $data = [] ;
        foreach ($areas as $area)
        {
            $data[] = ['id'=>$area->id , 'name'=>$area->name];
        }

        return $data;

    }
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run


        $response = $this->traitStore();
        return $response;
    }

}
