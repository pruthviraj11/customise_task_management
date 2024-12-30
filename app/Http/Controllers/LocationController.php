<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\CreateLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Models\Location;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\RoleService;
use App\Services\LocationService;
use Spatie\Permission\Models\Permission;

use Yajra\DataTables\Facades\DataTables;


use Illuminate\Http\Request;

class LocationController extends Controller
{

    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
        // $this->roleService = $roleService;
        $this->middleware('permission:locations-list|locations-create|locations-edit|locations-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:locations-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:locations-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:locations-delete', ['only' => ['destroy']]);

        //  Permission::create(['name' => 'locations-list', 'guard_name' => 'web', 'module_name' => 'Locations']);
        //  Permission::create(['name' => 'locations-create', 'guard_name' => 'web', 'module_name' => 'Locations']);
        //  Permission::create(['name' => 'locations-edit', 'guard_name' => 'web', 'module_name' => 'Locations']);
        //  Permission::create(['name' => 'locations-delete', 'guard_name' => 'web', 'module_name' => 'Locations']);

    }

    public function index()
    {
        $data['total_location'] = Location::count();
        $data['location'] = Location::get();

        return view('content.apps.location.list', compact('data'));
    }

    public function getAll()
    {
        $locations = $this->locationService->getAllLocation();

        return DataTables::of($locations)->addColumn('actions', function ($row) {
            $encryptedId = encrypt($row->id);
            $updateButton = "<a class='btn btn-warning  '  href='" . route('app-locations-edit', $encryptedId) . "'><i class='ficon' data-feather='edit'></i></a>";

            $deleteButton = "<a class='btn btn-danger confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-locations-destroy', $encryptedId) . "'><i class='ficon' data-feather='trash-2'></i></a>";

            return $updateButton . " " . $deleteButton;
        })->rawColumns(['actions'])->make(true);
    }

    public function create()
    {
        $page_data['page_title'] = "Location";
        $page_data['form_title'] = "Add New Location";
        $location = '';
        $users = User::where('status', '1')->get();
        $locationslist = $this->locationService->getAllLocation();
        $data['location'] = Location::all();
        return view('.content.apps.location.create-edit', compact('page_data', 'location', 'locationslist', 'data', 'users'));
    }


    public function store(CreateLocationRequest $request)
    {
        try {

            $locationData['location_name'] = $request->get('location_name');
            $locationData['address'] = $request->get('address');
            $locationData['created_by'] = auth()->user()->id;
            $locationData['status'] = $request->get('status');
            $location = $this->locationService->create($locationData);


            if (!empty($location)) {
                return redirect()->route("app-locations-list")->with('success', 'Location Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Location');
            }
        } catch (\Exception $error) {
            // dd($error->getMessage());
            return redirect()->route("app-locations-list")->with('error', 'Error while adding Location');
        }
    }

    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $location = $this->locationService->getLocation($id);
            $page_data['page_title'] = "Location";
            $page_data['form_title'] = "Edit Location";
            $users = User::where('status', '1')->get();
            $locationslist = $this->locationService->getAllLocation();
            $data['location'] = Location::all();


            return view('.content.apps.location.create-edit', compact('page_data', 'location', 'data', 'locationslist', 'users'));
        } catch (\Exception $error) {
            return redirect()->route("app-locations-list")->with('error', 'Error while editing Location');
        }
    }


    public function update(UpdateLocationRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $locationData['location_name'] = $request->get('location_name');
            $locationData['address'] = $request->get('address');
            $locationData['updated_by'] = auth()->user()->id;
            $locationData['status'] = $request->get('status') ? "on" : "off";

            $updated = $this->locationService->updateLocation($id, $locationData);


            if (!empty($updated)) {
                return redirect()->route("app-locations-list")->with('success', 'Location Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Location');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-locations-list")->with('error', 'Error while editing Location');
        }
    }

    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->locationService->deleteLocation($id);
            if (!empty($deleted)) {
                return redirect()->route("app-locations-list")->with('success', 'Locations Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Locations');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-locations-list")->with('error', 'Error while deleting Locations');
        }
    }
}
