<?php

namespace App\Http\Controllers;

use App\Models\Expenses;

//Models
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Request as RequestFilter;

class ExpensesController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {

		$from = RequestFilter::query('from');
		$to = RequestFilter::query('to');

		if (empty($from) && empty($to)) {
			$startDate = Carbon::now()->startOfDay();
			$endDate = Carbon::now()->endOfDay();
		} else {
			$startDate = Carbon::parse($from)->startOfDay();
			$endDate = Carbon::parse($to)->endOfDay();
		}
		$expenses = Expenses::whereBetween('date', [$startDate, $endDate])->with('user')->get();

		$mode = 'add';
		return view('admin.expenses.list', compact('expenses', 'mode', 'startDate', 'endDate'));
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
		//Validate form
		$request->validate([
			'date' => ['required'],
			'title' => ['required'],
			'total_amount' => ['required'],
		]);

		//Save Expenses
		$expense = [
			'user_id' => Auth::user()->id,
			'date' => ($request->date == '' ? NULL : Carbon::parse($request->date)->format('Y-m-d')),
			'title' => $request->title,
			'description' => $request->description,
			'total_amount' => $request->total_amount,
			'created_at' => Carbon::now(),
		];

		$isSaved = Expenses::insertGetId($expense);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Expense added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/expenses');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show() {
		$from = RequestFilter::query('from');
		$to = RequestFilter::query('to');

		if (empty($from) && empty($to)) {
			$startDate = Carbon::now()->startOfDay();
			$endDate = Carbon::now()->endOfDay();
		} else {
			$startDate = Carbon::parse($from)->startOfDay();
			$endDate = Carbon::parse($to)->endOfDay();
		}
		$expenses = Expenses::whereBetween('date', [$startDate, $endDate])->with('user')->get();

		return view('admin.expenses.print', compact('expenses'));
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
	public function update(Request $request, $id) {
		//
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
