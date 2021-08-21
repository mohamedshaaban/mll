<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CarTypesRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CarTypesCrudController extends CrudController
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
        CRUD::setModel(\App\Models\CarTypes::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/cartypes');
        CRUD::setEntityNameStrings('Car Type', 'Car Type');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns(['name_en', 'name_ar']); // add multiple columns, at the end of the stack


    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name_en',
            'label' => 'Name en',
            'type'  => 'text',
            'tab'   => 'Texts',

            // optional
            //'prefix' => '',
            //'suffix' => '',
            //'default'    => 'some value', // default value
            //'hint'       => 'Some hint text', // helpful text, show up after input
            //'attributes' => [
            //'placeholder' => 'Some text when empty',
            //'class' => 'form-control some-class'
            //], // extra HTML attributes and values your input might need
            //'wrapperAttributes' => [
            //'class' => 'form-group col-md-12'
            //], // extra HTML attributes for the field wrapper - mostly for resizing fields
            //'readonly'=>'readonly',
        ]);
        CRUD::addField([ // Text
            'name'  => 'name_ar',
            'label' => 'Name ar',
            'type'  => 'text',
            'tab'   => 'Texts',

            // optional
            //'prefix' => '',
            //'suffix' => '',
            //'default'    => 'some value', // default value
            //'hint'       => 'Some hint text', // helpful text, show up after input
            //'attributes' => [
            //'placeholder' => 'Some text when empty',
            //'class' => 'form-control some-class'
            //], // extra HTML attributes and values your input might need
            //'wrapperAttributes' => [
            //'class' => 'form-group col-md-12'
            //], // extra HTML attributes for the field wrapper - mostly for resizing fields
            //'readonly'=>'readonly',
        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
