<?php

namespace App\Http\Controllers;

//Models
use App\Models\Employees;
use App\Models\Invoice;
use App\Models\InvoiceDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Request as RequestFilter;

class EmployeesController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$employees = Employees::get();
		$mode = 'add';
		return view('admin.employee.list', compact('mode', 'employees'));
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
			'employee_name' => ['required'],
			'phone_primary' => ['required'],
		]);

		//Save Employee
		$employee = [
			'name' => $request->employee_name,
			'dob' => ($request->birthdate == '' ? NULL : Carbon::parse($request->birthdate)->format('Y-m-d')),
			'email' => $request->email,
			'phone_primary' => $request->phone_primary,
			'address' => $request->address,
			'created_at' => Carbon::now(),
		];

		$isSaved = Employees::insertGetId($employee);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Employee added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/employees');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($employeeId) {
		$from = RequestFilter::query('from');
		$to = RequestFilter::query('to');

		if (empty($from) && empty($to)) {
			$startDate = Carbon::now()->subDays(6)->startOfDay();
			$endDate = Carbon::now()->endOfDay();
		} else {
			$startDate = Carbon::parse($from)->startOfDay();
			$endDate = Carbon::parse($to)->endOfDay();
		}

		$invoices = $this->getEmployeeReportsByFilter($startDate, $endDate, $employeeId);

		return view('admin.employee.detail', compact('invoices', 'employeeId', 'startDate', 'endDate'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($employeeId) {
		$employee = Employees::where('id', '=', $employeeId)->first();
		$employees = Employees::get();
		$mode = 'edit';

		return view('admin.employee.list', compact('mode', 'employee', 'employees'));
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
			'employee_name' => ['required'],
			'phone_primary' => ['required'],
		]);

		//Update Employee
		$employee = [
			'name' => $request->employee_name,
			'dob' => ($request->birthdate == '' ? NULL : Carbon::parse($request->birthdate)->format('Y-m-d')),
			'email' => $request->email,
			'phone_primary' => $request->phone_primary,
			'address' => $request->address,
			'created_at' => Carbon::now(),
		];

		$employeeId = $request->employee_id;

		$isUpdated = Employees::where('id', '=', $employeeId)->update($employee);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Employee updated successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/employees');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$employeeId = $request->employee_id;

		//Delete Employee
		$isDeleted = Employees::where('id', '=', $employeeId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Employee deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/employees');
		}
	}

	public function printEmployeeReports($employeeId) {
		$from = RequestFilter::query('from');
		$to = RequestFilter::query('to');

		if (empty($from) && empty($to)) {
			$startDate = Carbon::now()->subDays(6)->startOfDay();
			$endDate = Carbon::now()->endOfDay();
		} else {
			$startDate = Carbon::parse($from)->startOfDay();
			$endDate = Carbon::parse($to)->endOfDay();
		}

		$invoices = $this->getEmployeeReportsByFilter($startDate, $endDate, $employeeId);

		return view('admin.employee.print', compact('invoices'));
	}

	public function getEmployeeReportsByFilter($startDate, $endDate, $employeeId) {
		$invoicesArr = Invoice::whereBetween('bill_date', [$startDate, $endDate])->where('is_settle_debit', '=', 0)->with('client')->get()->toArray();

		$employee = Employees::where('id', '=', $employeeId)->first();

		$invoices = array();

		if (!empty($invoicesArr)) {
			foreach ($invoicesArr as $key => $invoice) {
				$invoiceDetailsArr = InvoiceDetails::where('invoice_id', '=', $invoice['id'])->get()->toArray();

				$invoiceDetails = array();
				foreach ($invoiceDetailsArr as $invoiceDetail) {
					$beauticianArr = explode(',', $invoiceDetail['beautician_id']);

					if (in_array($employeeId, $beauticianArr)) {
						array_push($invoiceDetails, $invoiceDetail);
					}
				}
				if (!empty($invoiceDetails)) {
					$invoice['invoice_detail'] = $invoiceDetails;
					array_push($invoices, $invoice);
				}
			}
		}

		return $invoices;
	}
}
