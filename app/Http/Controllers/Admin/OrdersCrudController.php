<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Orders;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class OrdersCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Orders::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/orders');
        CRUD::setEntityNameStrings('order', 'orders');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['invoice_unique_id']); // add multiple columns, at the end of the stack

        $this->crud->addColumn([ // Text
            'name'  => 'customers',
            'label' => 'Customer',
            'type'      => 'relationship'
        ]);

        $this->crud->addColumn([ // Text
            'name'  => 'cars',
            'label' => 'car',
            'type'      => 'relationship'
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'requeststatus',
            'label' => 'status',
            'type'      => 'relationship'
        ]);

        $this->crud->addColumn([ // Text
            'name'  => 'areafrom',
            'label' => 'From',
            'type'      => 'relationship'
        ]);

        $this->crud->addColumn([ // Text
            'name'  => 'areato',
            'label' => 'To',
            'type'      => 'relationship'
        ]);



    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'invoice_unique_id',
            'label' => 'Invoice Id',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([  // Select2
            'label'     => 'Customer',
            'type'      => 'select2',
            'name'      => 'customer_id', // the db column for the foreign key
            'entity'    => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user

            'tab' => 'Texts',
        ]);
        CRUD::addField([  // Select2
            'label'     => 'Paid By',
            'type'      => 'select2',
            'name'      => 'paidby', // the db column for the foreign key
            'entity'    => 'paidby', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user

            'tab' => 'Texts',
        ]);
        CRUD::addField([  // Select2
            'label'     => 'Car',
            'type'      => 'select2',
            'name'      => 'car_id', // the db column for the foreign key
            'entity'    => 'cars', // the method that defines the relationship in your Model
            'attribute' => 'car_plate_id', // foreign key attribute that is shown to user

            'tab' => 'Texts',
        ]);
CRUD::addField([  // Select2
            'label'     => 'Area From',
            'type'      => 'relationship',
            'name'      => 'area_from', // the db column for the foreign key
            'entity'    => 'areafrom', // the method that defines the relationship in your Model
    'attribute' => 'name_en', // foreign key attribute that is shown to use
    'tab' => 'Texts',
    'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
    'data_source' => url("/admin/fetch/areas"), // url to controller search function (with /{id} should return model)

    'inline_create' => [ // specify the entity in singular
        'entity' => 'areas', // the entity in singular
        'force_select' => true, // should the inline-created entry be immediately selected?
        'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
        'modal_route' => route('areas-inline-create'), // InlineCreate::getInlineCreateModal()
        'create_route' =>  route('areas-inline-create-save'), // InlineCreate::storeInlineCreate()
        'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
    ]
        ]);
CRUD::addField([  // Select2
            'label'     => 'Area To',
            'type'      => 'relationship',
            'name'      => 'area_to', // the db column for the foreign key
            'entity'    => 'areato', // the method that defines the relationship in your Model
            'attribute' => 'name_en', // foreign key attribute that is shown to use
            'tab' => 'Texts',
     'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
     'data_source' => url("/admin/fetch/areas"), // url to controller search function (with /{id} should return model)

    'inline_create' => [ // specify the entity in singular
        'entity' => 'areas', // the entity in singular
        'force_select' => true, // should the inline-created entry be immediately selected?
        'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
        'modal_route' => route('areas-inline-create'), // InlineCreate::getInlineCreateModal()
        'create_route' =>  route('areas-inline-create-save'), // InlineCreate::storeInlineCreate()
        'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
    ]
        ]);
CRUD::addField([  // Select2
            'label'     => 'Driver',
            'type'      => 'select2',
            'name'      => 'driver_id', // the db column for the foreign key
            'entity'    => 'driver', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to use
            'tab' => 'Texts',
        ]);
CRUD::addField([  // Select2
            'label'     => 'Status',
            'type'      => 'select2',
            'name'      => 'status', // the db column for the foreign key
            'entity'    => 'requeststatus', // the method that defines the relationship in your Model
            'attribute' => 'name_en', // foreign key attribute that is shown to use
            'tab' => 'Texts',
        ]);

        CRUD::addField([ // Text
            'name'  => 'address',
            'label' => 'Address',
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);

//
//        CRUD::addField([ // Text
//            'name'  => 'comission',
//            'label' => 'Comission',
//            'type'  => 'radio',
//            'tab'   => 'Texts',
//
//        ]);
//
//
//        CRUD::addField([ // Text
//            'name'  => 'comission_paid',
//            'label' => 'Comission Paid',
//            'type'  => 'radio',
//            'tab'   => 'Texts',
//            'options'     => [
//                // the key will be stored in the db, the value will be shown as label;
//                0 => "Not Paid",
//                1 => "Paid"
//            ],
//
//        ]);

        CRUD::addField([ // Text
            'name'  => 'date',
            'label' => 'Date',
            'type'  => 'date',
            'tab'   => 'Texts',

        ]);




        CRUD::addField([ // Text
            'name'  => 'time',
            'label' => 'Time',
            'type'  => 'time',
            'tab'   => 'Texts',

        ]);




        CRUD::addField([ // Text
            'name'  => 'amount',
            'label' => 'Amount',
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);





        CRUD::addField([ // Text
            'name'  => 'payment_type',
            'label' => 'Payment Type',
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                Orders::CASH_PAYMENT => "Cash",
                Orders::KNET_PAYMENT => "Knet",
//                Orders::LATE_PAYMENT => "Late"
            ],

        ]);

        CRUD::addField([ // Text
            'name'  => 'is_paid',
            'label' => 'Is Paid',
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "Not Paid",
                1 => "Paid"
            ],

        ]);


        CRUD::addField([ // Text
            'name'  => 'payment_link',
            'label' => 'Payment Link',
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);




        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
