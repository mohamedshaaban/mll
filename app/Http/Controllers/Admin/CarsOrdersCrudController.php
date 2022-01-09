<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CarsRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Cars;
use App\Models\Customers;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class CarsOrdersCrudController extends CrudController
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

        CRUD::setModel(\App\Models\CarsOrders::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/cars');
        CRUD::setEntityNameStrings(trans('admin.car'), trans('admin.cars'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([ // Text
            'name'  => 'car_plate_id',
            'label' => trans('admin.Car Plate Id')

        ]);


        $this->crud->disablePersistentTable();
        $this->crud->removeButton('create');

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'car_plate_id',
            'label' => trans('admin.Plate Id'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);


        $this->crud->removeAllFilters();
        $this->crud->addFilter([
            'name'  => 'customer_id',
            'type'  => 'select2',
            'label' => trans('admin.Customer')
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
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $areas = Cars::where('car_plate_id','like','%'.$request->q.'%')->get(['id','car_plate_id']);
        $data = [] ;
        foreach ($areas as $area)
        {
            $data[] = ['id'=>$area->id , 'car_plate_id'=>$area->car_plate_id];
        }

        return $data;

    }
}
