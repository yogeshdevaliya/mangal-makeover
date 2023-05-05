<?php

namespace App\Http\Controllers;

//Models
use App\Models\Package;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PackageController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//Get Package
		$packages = Package::get();

		$mode = 'add';

		//Get Services
		$services = Services::get();

		return view('admin.package.list', compact('packages', 'mode', 'services'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//Get Services
		$services = Services::get();

		$mode = 'add';

		return view('admin.package.form', compact('services', 'mode'));
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
			'package_name' => ['required'],
			// 'service' => ['required'],
			'price' => ['required'],
		]);

		//Save Package
		$package = [
			'name' => $request->package_name,
			'slug' => str_slug($request->package_name, '-'),
			'service' => $request->service,
			'price' => $request->price,
			'expire_date' => ($request->expire_date == '' ? NULL : Carbon::parse($request->expire_date)->format('Y-m-d H:s:i')),
			'description' => $request->description,
			'created_at' => Carbon::now(),
		];

		$isSaved = Package::insertGetId($package);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Package added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/package');
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
	public function edit($packageId) {
		//Get Services
		$services = Services::get();

		//Get Package
		$package = Package::where('id', '=', $packageId)->first();

		$mode = 'edit';

		//Get Package
		$packages = Package::get();

		return view('admin.package.list', compact('packages', 'mode', 'services', 'package'));
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
			'package_name' => ['required'],
			// 'service' => ['required'],
			'price' => ['required'],
		]);

		//Update Package
		$package = [
			'name' => $request->package_name,
			'slug' => str_slug($request->package_name, '-'),
			'service' => $request->service,
			'price' => $request->price,
			'expire_date' => ($request->expire_date == '' ? NULL : Carbon::parse($request->expire_date)->format('Y-m-d H:s:i')),
			'description' => $request->description,
			'updated_at' => Carbon::now(),
		];

		$packageId = $request->package_id;

		$isUpdated = Package::where('id', '=', $packageId)->update($package);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Package updated successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/package');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$packageId = $request->package_id;

		//Delete Package
		$isDeleted = Package::where('id', '=', $packageId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Package deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/package');
		}
	}
}
