<?php

namespace App\Http\Controllers;

//Models
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductsController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//Get Products
		$products = Products::get();

		//Add Products
		$mode = 'add';

		return view('admin.products.list', compact('mode', 'products'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//Add Products
		$mode = 'add';

		return view('admin.products.form', compact('mode'));
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
			'product_name' => ['required'],
			'price' => ['required'],
		]);

		//Save Product
		$service = [
			'name' => $request->product_name,
			'slug' => str_slug($request->product_name, '-'),
			'price' => $request->price,
			'description' => $request->description,
			'created_at' => Carbon::now(),
		];

		$isSaved = Products::insertGetId($service);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Product added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/products');
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
	public function edit($productId) {
		//Get Product
		$product = Products::where('id', '=', $productId)->first();

		//Get Products
		$products = Products::get();

		$mode = 'edit';

		return view('admin.products.list', compact('mode', 'products', 'product'));
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
			'product_name' => ['required'],
			'price' => ['required'],
		]);

		//Update Product
		$product = [
			'name' => $request->product_name,
			'slug' => str_slug($request->product_name, '-'),
			'price' => $request->price,
			'description' => $request->description,
			'updated_at' => Carbon::now(),
		];

		$productId = $request->product_id;

		$isUpdated = Products::where('id', '=', $productId)->update($product);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Product updated successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/products');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$productId = $request->product_id;

		//Delete Product
		$isDeleted = Products::where('id', '=', $productId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Product deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/products');
		}
	}
}
