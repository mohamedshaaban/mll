<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Request;
use App\Models\Customers;
use App\Models\Invoices;
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

//        $request->input('name');
        $id = (request()->route('id'));
        if($id) {
            $invoice = Invoices::with('orders')->whereId($id)->first();
             if($invoice) {
                $orders = Orders::where('paid_by', $invoice->customer_id)->where('is_paid', Orders::ORDER_NOT_PAID)->where('status', Orders::COMPLETED_ORDER)->get();
                session(['orders' => $orders]);
                session(['invoice' => $invoice->orders]);
            }
        }
//        CRUD::setValidation(StoreRequest::class);

        CRUD::addField(  // Text
            [   // view
                'name' => 'invoice_unique_id',
                'label' => trans('admin.invoice_unique_id'),
                'type' => 'text',
                 'tab' => 'Texts',
            ]);
        CRUD::addField(  // Text
            [   // view
                'name' => 'url',
                'label' => trans('admin.Url'),
                'type' => 'text',
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




        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }
    public function update()
    {
        //Get Total Amount of payment
        $totalPaidAmt  = 0 ;
        $totalOrderAmt  = 0 ;
        $totalAmt  = 0 ;
        foreach(json_decode($_REQUEST['payments']) as $payment){
            $totalAmt += $payment->amount;
        }
        //End OF Get Total Amount of payment
        //Get Total Amount of Orders
//        dump($totalAmt);


        foreach(($_REQUEST['orderId']) as $orderId){
            $order = Orders::find($orderId);
            $totalOrderAmt += $order->amount - $order->discount;

        }
        //Total PAyment Cant be greater than total orders
        if($totalAmt > $totalOrderAmt)
        {
            return \Redirect::back()->withErrors(['Total Payment Cant be greater than total of orders']);
        }

        //End OF Get Total Amount of Orders

        $previousInvoice = Invoices::find($_REQUEST['id']);
        $previousOrders = $previousInvoice->orders;

        $response = $this->traitUpdate();

        $newInvoice = Invoices::find($this->crud->entry->id);
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
