<?php

namespace App\Http\Controllers;

//Models
//Models
use App\Models\ServiceCategory;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoriesController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//Get Service Category
		$serviceCategories = ServiceCategory::get();

		//Add Service Category
		$serviceCategoryMode = 'add';

		//Get Services
		$services = Services::with('category')->get();

		$serviceMode = 'add';

		return view('admin.service_categories.list', compact('serviceCategories', 'serviceCategoryMode', 'services', 'serviceMode'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//Add Service Category
		$mode = 'add';

		return view('admin.service_categories.form', compact('mode'));
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
			'category_name' => ['required'],
		]);

		//Save Service Category
		$category = [
			'name' => $request->category_name,
			'slug' => str_slug($request->category_name, '-'),
			'description' => $request->description,
			'created_at' => Carbon::now(),
		];

		$isSaved = ServiceCategory::insertGetId($category);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Service category added successfully.';
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
	public function edit($categoryId) {
		//Get Service Category
		$serviceCategory = ServiceCategory::where('id', '=', $categoryId)->first();

		//Get Service Category
		$serviceCategories = ServiceCategory::get();

		$serviceCategoryMode = 'edit';

		//Get Services
		$services = Services::with('category')->get();

		$serviceMode = 'add';

		return view('admin.service_categories.list', compact('serviceCategory', 'serviceCategories', 'serviceCategoryMode', 'serviceMode', 'services'));
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
			'category_name' => ['required'],
		]);

		//Update Service Category
		$category = [
			'name' => $request->category_name,
			'slug' => str_slug($request->category_name, '-'),
			'description' => $request->description,
			'updated_at' => Carbon::now(),
		];

		$categoryId = $request->category_id;

		$isUpdated = ServiceCategory::where('id', '=', $categoryId)->update($category);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Service category updated successfully.';
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
		$categoryId = $request->category_id;

		//Delete Service Category
		$isDeleted = ServiceCategory::where('id', '=', $categoryId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Service category deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/categories');
		}
	}
}
