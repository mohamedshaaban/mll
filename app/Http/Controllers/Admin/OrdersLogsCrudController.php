<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AreaRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Areas;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use http\Client\Request;
use Illuminate\Support\Facades\App;

class OrdersLogsCrudController extends CrudController
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
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\OrdersHistory::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/orderslogs');
        CRUD::setEntityNameStrings(trans('admin.area'), trans('admin.orderslogs'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(
            [
                'name'=>'user',
                'label' => trans('admin.user')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'order',
                'attribute'=>'invoice_unique_id',
                'label' => trans('admin.order')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'field',
                'attribute'=>'field',
                'label' => trans('admin.field')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'old_value',
                'attribute'=>'old_value',
                'label' => trans('admin.old_value')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'new_value',
                'attribute'=>'new_value',
                'label' => trans('admin.new_value')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'order',
                'attribute'=>'invoice_unique_id',
                'label' => trans('admin.order')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'text',
                'label' => trans('admin.Text')
            ]
        );

        $this->crud->disableBulkActions();
        $this->crud->removeAllButtons();

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name_en',
            'label' => trans('admin.Name en'),
            'type'  => 'text',
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'name_ar',
            'label' => trans('admin.Name ar'),
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
