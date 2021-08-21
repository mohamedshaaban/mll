<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Orders;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ComissionsCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix').'/comissions');
        CRUD::setEntityNameStrings('comission', 'comissions');
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
        CRUD::addField([ // Text
            'name'  => 'comission',
            'label' => 'Comission',
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
//
//
        CRUD::addField([ // Text
            'name'  => 'comission_paid',
            'label' => 'Comission Paid',
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "Not Paid",
                1 => "Paid"
            ],

        ]);





        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
