<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Request as RequestFilter;

class ReportsController extends Controller {
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
		$invoices = $this->getInvoiceByDateFilter($startDate, $endDate);
		$total = [
			'service' => number_format($invoices->sum('service_total'), 2),
			'product' => number_format($invoices->sum('product_total'), 2),
			'package' => number_format($invoices->sum('package_total'), 2),
			'discount' => number_format($invoices->sum('discount'), 2),
			'advance_payment' => number_format($invoices->sum('advance_payment'), 2),
			'debit_amount' => number_format($invoices->sum('debit_amount'), 2),
			'amount_paid' => number_format($invoices->sum('amount_paid'), 2),
			'on_credit_cash' => number_format($invoices->sum('on_credit_cash'), 2),
			'on_credit_debit' => number_format($invoices->sum('on_credit_debit'), 2),
		];
		return view('admin.reports.list', compact('invoices', 'total', 'startDate', 'endDate'));
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
		$from = RequestFilter::query('from');
		$to = RequestFilter::query('to');

		if (empty($from) && empty($to)) {
			$startDate = Carbon::now()->startOfDay();
			$endDate = Carbon::now()->endOfDay();
		} else {
			$startDate = Carbon::parse($from)->startOfDay();
			$endDate = Carbon::parse($to)->endOfDay();
		}

		$invoices = $this->getInvoiceByDateFilter($startDate, $endDate);

		return view('admin.reports.print', compact('invoices'));
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

	public function getInvoiceByDateFilter($startDate, $endDate) {
		$invoices = Invoice::whereBetween('bill_date', [$startDate, $endDate])->with('invoice_details')->with('user')->with('client')->get();

		foreach ($invoices as $key => $invoice) {
			$serviceTotal = 0;
			$productTotal = 0;
			$packageTotal = 0;

			if (count($invoice->invoice_details) > 0) {
				foreach ($invoice->invoice_details as $key1 => $invoiceDetail) {
					if ($invoiceDetail->item_type == 'SERVICE') {
						$serviceTotal = $serviceTotal + $invoiceDetail->total_price;
					}
					if ($invoiceDetail->item_type == 'PRODUCT') {
						$productTotal = $productTotal + $invoiceDetail->total_price;
					}
					if ($invoiceDetail->item_type == 'PACKAGE') {
						$packageTotal = $packageTotal + $invoiceDetail->total_price;
					}
				}
			}

			$invoices[$key]->service_total = $serviceTotal;
			$invoices[$key]->product_total = $productTotal;
			$invoices[$key]->package_total = $packageTotal;
		}

		return $invoices;
	}
}
