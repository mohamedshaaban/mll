<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Customers;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CustomersCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Customers::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customers');
        CRUD::setEntityNameStrings('Customer', 'Customer');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['name', 'mobile']); // add multiple columns, at the end of the stack


    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => 'Name',
            'type'  => 'text',
            'tab'   => 'Texts',


        ]);
        CRUD::addField([ // Text
            'name'  => 'mobile',
            'label' => 'Mobile',
            'type'  => 'text',
            'tab'   => 'Texts',


        ]);

        CRUD::addField([ // Text
            'name'  => 'type',
            'label' => 'Type',
            'type' => 'select_from_array',
            'options' => [Customers::CUSTOMER=>'customer',Customers::GARAGE=>'garage'],
            'allows_null' => false,
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [Customers::ACTIVE=>'Active',Customers::BLOCK=>'Block'],
            'allows_null' => false,
            'tab'   => 'Texts',
        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
