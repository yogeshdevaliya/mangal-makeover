<?php

namespace App\Http\Controllers;

//Models
use App\Models\RunningServices;
use App\Models\ServiceCategory;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServicesController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//Get Services
		$services = Services::with('category')->get();

		return view('admin.service.list', compact('services'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//Get Service
		$serviceCategories = ServiceCategory::get();

		$mode = 'add';

		return view('admin.service.form', compact('serviceCategories', 'mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//Validate form
		$request->validate([
			'service_name' => ['required'],
			'service_category' => ['required'],
			// 'duration' => ['required'],
			'price' => ['required'],
		]);

		//Save Service
		$service = [
			'name' => $request->service_name,
			'slug' => str_slug($request->service_name, '-'),
			'service_category_id' => $request->service_category,
			'duration' => $request->duration,
			'price' => $request->price,
			'description' => $request->description,
			'created_at' => Carbon::now(),
		];

		$isSaved = Services::insertGetId($service);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Service added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/categories');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($serviceId) {

		//Get Service
		$service = Services::where('id', '=', $serviceId)->first();

		//Get Service Category
		$serviceCategories = ServiceCategory::get();

		//Add Service Category
		$serviceCategoryMode = 'add';

		//Get Services
		$services = Services::with('category')->get();

		$serviceMode = 'edit';

		return view('admin.service_categories.list', compact('serviceCategories', 'serviceCategoryMode', 'services', 'serviceMode', 'service'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request) {
		//Validate form
		$request->validate([
			'service_name' => ['required'],
			'service_category' => ['required'],
			// 'duration' => ['required'],
			'price' => ['required'],
		]);

		//Update Service
		$service = [
			'name' => $request->service_name,
			'slug' => str_slug($request->service_name, '-'),
			'service_category_id' => $request->service_category,
			'duration' => $request->duration,
			'price' => $request->price,
			'description' => $request->description,
			'updated_at' => Carbon::now(),
		];

		$serviceId = $request->service_id;

		$isUpdated = Services::where('id', '=', $serviceId)->update($service);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Service updated successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/categories');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$serviceId = $request->service_id;

		//Delete Service
		$isDeleted = Services::where('id', '=', $serviceId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Service deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/services');
		}
	}

	public function addRunningService(Request $request) {
		$client_id = $request->input('client_id');
		$services = $request->input('service');
		foreach ($services as $key => $value) {
			$running_service = RunningServices::where('client_id', '=', $client_id)->delete();
		}
		foreach ($services as $key => $value) {
			$running_service = RunningServices::insert(['client_id' => $client_id, 'service_id' => $value, 'created_at' => Carbon::now()]);
		}
		if ($running_service) {
			$res['status'] = 'success';
			$action = $request->input('action');
			if ($action == 'add') {
				$res['message'] = 'Services added successfully!';
			} else {
				$res['message'] = 'Services updated successfully!';
			}
			$request->session()->flash('res', $res);
			return redirect('/dashboard');
		}
	}

}
