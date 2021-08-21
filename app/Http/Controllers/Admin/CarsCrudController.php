<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CarsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Customers;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CarsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Cars::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/cars');
        CRUD::setEntityNameStrings('car', 'cars');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([ // Text
            'name'  => 'car_plate_id',
            'label' => 'Car Plate Id'

        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'car_model',
            'label' => 'Car Model'

        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'cartypes',
            'label' => 'Car Type'

        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'customername',
            'label' => 'Customer Name'

        ]);


    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'car_plate_id',
            'label' => 'Plate Id',
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'car_model',
            'label' => 'Model',
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([  // Select2
            'label'     => 'Customer',
            'type'      => 'select2',
            'name'      => 'customer_id', // the db column for the foreign key
            'entity'    => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            // 'wrapperAttributes' => [
            //     'class' => 'form-group col-md-6'
            //   ], // extra HTML attributes for the field wrapper - mostly for resizing fields
            'tab' => 'Texts',
        ]);

        CRUD::addField([  // Select2
            'label'     => 'Car Type',
            'type'      => 'select2',
            'name'      => 'car_type_id', // the db column for the foreign key
            'entity'    => 'cartypes', // the method that defines the relationship in your Model
            'attribute' => 'name_en', // foreign key attribute that is shown to user
            // 'wrapperAttributes' => [
            //     'class' => 'form-group col-md-6'
            //   ], // extra HTML attributes for the field wrapper - mostly for resizing fields
            'tab' => 'Texts',
        ]);
//        $this->crud->addFilter([
//            'type'  => 'simple',
//            'name'  => 'active',
//            'label' => 'Active'
//        ],
//            false,
//            function() { // if the filter is active
//                // $this->crud->addClause('active'); // apply the "active" eloquent scope
//            } );
        $this->crud->removeAllFilters();
        $this->crud->addFilter([
            'name'  => 'customer_id',
            'type'  => 'select2',
            'label' => 'Customer'
        ], function () {
            return Customers::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
              $this->crud->addClause('where', 'customer_id', $value);
        });

        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
