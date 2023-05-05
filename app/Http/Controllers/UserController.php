<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show() {
		return view('password.change');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
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
			'old_password' => ['required'],
			'password' => ['required', 'string', 'min:6', 'confirmed'],
		]);

		$user = User::where('id', '=', Auth::user()->id)->first();

		if (!Hash::check($request->input('old_password'), $user->password)) {
			$res['status'] = 'danger';
			$res['message'] = 'Error! Invalid old password.';
		} else {
			$isUpdated = User::where('id', '=', Auth::user()->id)->update(['password' => bcrypt($request->input('password'))]);

			$res['status'] = 'success';
			$res['message'] = 'Password updated successfully.';
		}

		$request->session()->flash('res', $res);
		return redirect('admin/change/password');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
