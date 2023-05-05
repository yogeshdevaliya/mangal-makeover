<?php

namespace App\Http\Controllers;

//Models
use App\Models\Clients;
use App\Models\Employees;
use App\Models\Invoice;
use App\Models\InvoiceDetails;
use App\Models\Package;
use App\Models\Products;
use App\Models\RunningServices;
use App\Models\Services;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Request as RequestFilter;

class InvoiceController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$clientInvoices = Invoice::with('client')->get();

		foreach ($clientInvoices as $key => $clientInvoice) {
			$invoiceDetails = InvoiceDetails::where('invoice_id', '=', $clientInvoice->id)->get();

			foreach ($invoiceDetails as $key1 => $invoiceDetail) {
				$beauticianIds = explode(',', $invoiceDetail->beautician_id);
				$beauticianNames = array();

				foreach ($beauticianIds as $beauticianId) {
					$beauticianName = Employees::where('id', '=', $beauticianId)->pluck('name')->first();
					array_push($beauticianNames, $beauticianName);
				}

				$invoiceDetails[$key1]['beautician_name'] = implode(',', $beauticianNames);
			}
			$clientInvoices[$key]->invoice_details = $invoiceDetails;
		}

		return view('admin.invoices.list', compact('clientInvoices'));
	}

	public function getInvoiceData(Request $request)
	{
		if( isset($request->type) && $request->type == 'search' ) {
			$term = $request->term;
			$client_ids = Clients::where('name', 'LIKE', '%'.$term.'%')->pluck('id');

			$clientInvoices = Invoice::where('invoice_number', 'LIKE', '%'.$term.'%')
				->orWhere('bill_date', 'LIKE', '%'.$term.'%')->with('client')
				->orWhereIn('client_id', $client_ids)->paginate(10);
		} else {
			$clientInvoices = Invoice::with('client')->paginate(10);
		}
		foreach ($clientInvoices as $key => $clientInvoice) {
			$invoiceDetails = InvoiceDetails::where('invoice_id', '=', $clientInvoice->id)->get();
			foreach ($invoiceDetails as $key1 => $invoiceDetail) {
				$beauticianIds = explode(',', $invoiceDetail->beautician_id);
				$beauticianNames = array();
				foreach ($beauticianIds as $beauticianId) {
					$beauticianName = Employees::where('id', '=', $beauticianId)->pluck('name')->first();
					array_push($beauticianNames, $beauticianName);
				}
				$invoiceDetails[$key1]['beautician_name'] = implode(',', $beauticianNames);
			}
			$clientInvoices[$key]->invoice_details = $invoiceDetails;
		}
		return response()->json($clientInvoices);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		$mode = 'add';
		$path = $request->getQueryString();

		//Employees
		$employees = Employees::get();

		if (isset($path) && strlen($path) > 0) {
			$clientId = $request->client_id;

			//Get Client Detail
			$client = Clients::where('id', '=', $clientId)->first();

			//Get Client Running Services
			$running_services = RunningServices::where('client_id', '=', $clientId)->get();

			//Save Invoice and Redirect
			if ($request->from == 'save' && count($running_services) > 0) {
				//Delete All Running Services
				$isDeleted = $this->createInvoiceByRunningService($clientId, $request->from);
				$res['status'] = 'success';
				$res['message'] = 'Invoice saved successfully.';
				$request->session()->flash('res', $res);
				return redirect('/dashboard');
			} else {
				$mode = 'running';
				return view('admin.invoices.form', compact('mode', 'client', 'running_services', 'employees'));
			}
		}
		return view('admin.invoices.form', compact('mode', 'employees'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

		//Check Client Exist Or Not
		if (!empty($request->client_id)) {
			$clientId = (int) $request->client_id;
		} else {
			$client = Clients::where('name', '=', $request->client_name)->first();
			if (!empty($client)) {
				$clientId = (int) $client->id;
			} else {
				//Save Clients
				$clientArr = [
					'name' => $request->client_name,
					'phone_number' => $request->phone_number,
					'gender' => $request->gender,
					'dob' => ($request->birthdate == '' ? NULL : Carbon::parse($request->birthdate)->format('Y-m-d')),
					'created_at' => Carbon::now(),
				];

				$clientId = Clients::insertGetId($clientArr);
			}
		}

		//Settle Debit Amount
		if (!empty($request->is_settle_debit)) {
			$settleDebitAmount = (float) ($request->settle_debit_amount == '' ? 0 : $request->settle_debit_amount);
			$data = app('App\Http\Controllers\ClientsController')->settleDebitAmountByClientId($clientId, $settleDebitAmount);
		}

		//Advance Payment
		if (!empty($request->is_advance_payment)) {
			$clientAdvancePayment = (float) ($request->client_advance_payment == '' ? 0 : $request->client_advance_payment);
			$client_data = Clients::where('id', '=', $clientId)->first();
			$totalAdvance = (float) ($client_data->total_advance == '' ? 0 : $client_data->total_advance);
			$totalAdvance = $totalAdvance + $clientAdvancePayment;
			$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance]);
		}

		$invoiceArr = [
			'user_id' => Auth::user()->id,
			'bill_date' => ($request->billing_date == '' ? NULL : Carbon::parse($request->billing_date)->format('Y-m-d')),
			'grand_total' => $request->grand_total,
			'payment_type' => ($request->payment_type == '' ? 'CASH' : $request->payment_type),
			'notes' => $request->notes,
		];

		if ($request->mode == 'add' || $request->mode == 'running') {
			//Generate Unique Quote Number EX. 1 TO 0001
			$invoiceCount = Invoice::count();
			$invoiceNumber = trim(sprintf('%04u', $invoiceCount + 1));
			$invoiceArr['client_id'] = $clientId;
			$invoiceArr['invoice_number'] = $invoiceNumber;
			$invoiceArr['created_at'] = Carbon::now();
		}

		if ($request->mode == 'edit') {
			$invoice = Invoice::where('id', '=', $request->invoice_id)->first();
			$invoiceNumber = $invoice->invoice_number;
			$invoiceArr['updated_at'] = Carbon::now();
			$this->settleInvoiceDetailById($request->invoice_id);
		}

		$client_data = Clients::where('id', '=', $clientId)->first();

		if ($request->payment_type == 'CASH' || $request->payment_type == 'CARD' || $request->payment_type == 'ONLINE') {
			$invoiceArr['amount_paid'] = $request->grand_total;
		}

		//If payment type DEBIT save Debit Amount Or Amount Paid
		if ($request->payment_type == 'DEBIT') {
			$debitAmount = (float) ($request->debit_amount == '' ? 0 : $request->debit_amount);
			$amountPaid = (float) ($request->amount_paid == '' ? 0 : $request->amount_paid);
			$totalDebit = (float) ($client_data->total_debit == '' ? 0 : $client_data->total_debit);

			$invoiceArr['debit_amount'] = $debitAmount;
			$invoiceArr['amount_paid'] = $amountPaid;

			// Update total debit amount in client table
			$totalDebit = $totalDebit + $debitAmount;
			$client_update = Clients::where('id', '=', $clientId)->update(['total_debit' => $totalDebit]);
		}

		//If payment type ADVANCE PAYMENT save Advance Payment Or Debit Amount
		if ($request->payment_type == 'ADVANCE_PAYMENT') {
			$advancePayment = (float) ($request->advance_payment == '' ? 0 : $request->advance_payment);
			$amountPaid = (float) ($request->amount_paid == '' ? 0 : $request->amount_paid);
			$totalAdvance = (float) ($client_data->total_advance == '' ? 0 : $client_data->total_advance);
			$totalDebit = (float) ($client_data->total_debit == '' ? 0 : $client_data->total_debit);
			$grandTotal = (float) $request->grand_total;

			$paymentHistory = array();
			if ($advancePayment >= $totalDebit) {
				$advancePayment = $advancePayment - $totalDebit;
				$data = app('App\Http\Controllers\ClientsController')->settleDebitAmountByClientId($clientId, $totalDebit);
				$paymentHistory['debit_amount_paid'] = $totalDebit;
				$paymentHistory['debit_history'] = $data['debit_history'];
			} else {
				$data = app('App\Http\Controllers\ClientsController')->settleDebitAmountByClientId($clientId, $advancePayment);
				$paymentHistory['debit_amount_paid'] = $advancePayment;
				$paymentHistory['debit_history'] = $data['debit_history'];
				$advancePayment = 0;
			}

			if ($advancePayment >= $grandTotal) {
				// Update total debit amount in client table
				$totalAdvance = $totalAdvance + $advancePayment - $grandTotal;
				$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance]);

				$invoiceArr['advance_payment'] = $advancePayment - $grandTotal;
				$invoiceArr['amount_paid'] = $amountPaid;
				$invoiceArr['debit_amount'] = 0;
				$paymentHistory['advance_payment_credit'] = $advancePayment - $grandTotal;
				$paymentHistory['debit_amount_credit'] = 0;
			} else {
				//Get Client Detail
				$client = Clients::where('id', '=', $clientId)->first();
				$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);

				$debitAmount = $grandTotal - $advancePayment;
				// Update total debit amount in client table
				$totalDebit = $totalDebit + $debitAmount;
				$isUpdated = Clients::where('id', '=', $clientId)->update(['total_debit' => $totalDebit]);

				$invoiceArr['advance_payment'] = 0;
				$invoiceArr['amount_paid'] = $amountPaid;
				$invoiceArr['debit_amount'] = $debitAmount;
				$paymentHistory['advance_payment_credit'] = 0;
				$paymentHistory['debit_amount_credit'] = $debitAmount;
			}

			$invoiceArr['payment_history'] = json_encode($paymentHistory);
		}

		//If payment type ON CREDIT save amount paid
		if ($request->payment_type == 'ON_CREDIT' || $request->payment_type == 'ON_CREDIT+CASH') {
			$amountPaid = (float) ($request->amount_paid == '' ? 0 : $request->amount_paid);
			$totalAdvance = (float) ($client_data->total_advance == '' ? 0 : $client_data->total_advance);

			$grandTotal = (float) $request->grand_total;
			$invoiceArr['amount_paid'] = $amountPaid;

			if ($request->payment_type == 'ON_CREDIT+CASH') {
				$onCreditCash = (float) ($request->on_credit_cash == '' ? 0 : $request->on_credit_cash);
				$onCreditDueCash = (float) ($request->on_credit_due_cash == '' ? 0 : $request->on_credit_due_cash);
				$advanceCash = $onCreditCash - $onCreditDueCash;
				$totalAdvance = $totalAdvance - ($grandTotal - $onCreditDueCash);

				$paymentHistory = array();
				$paymentHistory['on_credit_due_cash'] = $onCreditDueCash;
				$paymentHistory['advance_payment_credit'] = $advanceCash;
				$paymentHistory['advance_payment_debit'] = $grandTotal - $onCreditDueCash;

				if ($advanceCash > 0) {
					$totalAdvance = $totalAdvance + $advanceCash;
				}
				$invoiceArr['on_credit_cash'] = $onCreditCash;
				$invoiceArr['payment_history'] = json_encode($paymentHistory);
			} else {
				$totalAdvance = $totalAdvance - $grandTotal;
			}
			// Update remain total advance
			$client_update = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance]);
		}

		//If payment type ON CREDIT save amount paid
		if ($request->payment_type == 'ON_CREDIT+DEBIT') {
			$amountPaid = (float) ($request->amount_paid == '' ? 0 : $request->amount_paid);
			$totalAdvance = (float) ($client_data->total_advance == '' ? 0 : $client_data->total_advance);
			$onCreditDebit = (float) ($request->on_credit_debit == '' ? 0 : $request->on_credit_debit);
			$onCreditDueDebit = (float) ($request->on_credit_due_debit == '' ? 0 : $request->on_credit_due_debit);
			$totalDebit = (float) ($client_data->total_debit == '' ? 0 : $client_data->total_debit);
			$grandTotal = (float) $request->grand_total;
			$totalAdvance = $totalAdvance - ($grandTotal - $onCreditDueDebit);

			$paymentHistory = array();
			$paymentHistory['on_credit_due_debit'] = $onCreditDueDebit;
			$paymentHistory['debit_amount_credit'] = $onCreditDebit;
			$paymentHistory['advance_payment_debit'] = $grandTotal - $onCreditDueDebit;

			$invoiceArr['amount_paid'] = $amountPaid;
			$invoiceArr['on_credit_debit'] = $onCreditDebit;
			$invoiceArr['payment_history'] = json_encode($paymentHistory);

			// Update remain total advance and total debit
			$totalDebit = $totalDebit + $onCreditDebit;
			$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance, 'total_debit' => $totalDebit]);
		}

		//Settle Debit Amount
		if (!empty($request->is_settle_debit)) {
			$settleDebitAmount = (float) ($request->settle_debit_amount == '' ? 0 : $request->settle_debit_amount);
			$invoiceArr['settle_debit_amount'] = $settleDebitAmount;
			$invoiceArr['amount_paid'] = $invoiceArr['amount_paid'] + $settleDebitAmount;
			$invoiceArr['notes'] = ($invoiceArr['notes'] == '' ? 'Invoice + Settle Debit' : $invoiceArr['notes'] . ', Invoice + Settle Debit');
		}

		//Advance Payment
		if (!empty($request->is_advance_payment)) {
			$clientAdvancePayment = (float) ($request->client_advance_payment == '' ? 0 : $request->client_advance_payment);
			$invoiceArr['client_advance_payment'] = $clientAdvancePayment;
			$invoiceArr['amount_paid'] = $invoiceArr['amount_paid'] + $clientAdvancePayment;
		}

		if ($request->mode == 'edit') {
			//Form mode edit so first delete all invoice details and save invoice
			$invoiceId = (float) $request->invoice_id;
			$isResetValue = $this->update($invoiceId);
			$isDeleted = InvoiceDetails::where('invoice_id', '=', $invoiceId)->delete();
			$isUpdated = Invoice::where('id', '=', $invoiceId)->update($invoiceArr);
		} else if ($request->mode == 'running') {
			//Form mode running so first delete all running services and save invoice
			$isDeleted = $this->createInvoiceByRunningService($clientId, 'running');
			$invoiceId = Invoice::insertGetId($invoiceArr);
		} else {
			$invoiceId = Invoice::insertGetId($invoiceArr);
		}

		if ($invoiceId) {
			//Save Invoice Detail
			$invoiceDetails = array_values($request->invoice_detail);

			$totalDiscount = (float) ($request->total_discount == '' ? 0 : $request->total_discount);
			$totalItemDiscount = 0;

			foreach ($invoiceDetails as $key => $invoiceDetail) {
				if (!empty($invoiceDetail)) {
					$itemDiscount = (float) ($invoiceDetail['discount'] == '' ? 0 : $invoiceDetail['discount']);
					$totalItemDiscount = $totalItemDiscount + $itemDiscount;

					$invoiceDetailArr = [
						'invoice_id' => $invoiceId,
						'item_id' => (float) ($invoiceDetail['item_id'] == '' ? 0 : $invoiceDetail['item_id']),
						'name' => $invoiceDetail['name'],
						'beautician_id' => implode(',', $invoiceDetail['beautician_id']),
						'quantity' => (float) $invoiceDetail['quantity'],
						'discount' => $itemDiscount,
						'price' => (float) $invoiceDetail['price'],
						'total_price' => (float) $invoiceDetail['total_price'],
						'item_type' => ($invoiceDetail['item_type'] == '' ? 'SERVICE' : $invoiceDetail['item_type']),
						'created_at' => Carbon::now(),
					];

					$invoiceDetailId = InvoiceDetails::insertGetId($invoiceDetailArr);
				}
			}

			if ($totalDiscount > 0) {
				$isDiscountUpdated = Invoice::where('id', '=', $invoiceId)->update(['total_discount' => $totalDiscount]);
			} else {
				if ($totalItemDiscount > 0) {
					$isDiscountUpdated = Invoice::where('id', '=', $invoiceId)->update(['total_discount' => $totalItemDiscount]);
				} else {
					$isDiscountUpdated = Invoice::where('id', '=', $invoiceId)->update(['total_discount' => 0]);
				}
			}

			if ($invoiceDetailId) {
				$res['status'] = 'success';
				$res['message'] = 'Invoice ' . ($request->mode == 'edit' ? 'updated' : 'saved') . ' successfully.';
				if ($request->submit == 'save') {
					$request->session()->flash('res', $res);
					return redirect('/admin/invoices');
				} elseif ($request->submit == 'generate') {
					return redirect('/admin/invoices/' . $invoiceId . '/print');
				}
			} else {
				$res['status'] = 'danger';
				$res['message'] = 'Error! ' . ($request->mode == 'edit' ? 'updating' : 'saving') . ' invoice detail.';
			}
		} else {
			$res['status'] = 'danger';
			$res['message'] = 'Error! ' . ($request->mode == 'edit' ? 'updating' : 'saving') . ' invoice detail.';
		}

		$request->session()->flash('res', $res);

		if ($request->submit == 'generate_bill' && $res['status'] == 'success') {
			return redirect('admin/invoices/' . $invoiceId . '/print');
		} else {
			return redirect('admin/invoices');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $invoiceId) {
		//Get Invoice Detail
		$invoice = Invoice::where('id', '=', $invoiceId)->with('client')->first();

		if (!empty($invoice)) {
			$invoiceDetails = InvoiceDetails::where('invoice_id', '=', $invoice->id)->get();

			foreach ($invoiceDetails as $key1 => $invoiceDetail) {
				$beauticianIds = explode(',', $invoiceDetail->beautician_id);
				$beauticianNames = array();

				foreach ($beauticianIds as $beauticianId) {
					$beauticianName = Employees::where('id', '=', $beauticianId)->pluck('name')->first();
					array_push($beauticianNames, $beauticianName);
				}

				$invoiceDetails[$key1]['beautician_name'] = implode(',', $beauticianNames);
			}
			$invoice->invoice_details = $invoiceDetails;

			return view('admin.invoices.detail', compact('invoice'));
		} else {
			$res['status'] = 'danger';
			$res['message'] = 'This invoice number does not exist.';
			$request->session()->flash('res', $res);
			return redirect('admin/invoices');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $invoiceId) {

		//Get Invoice Detail
		$invoice = Invoice::where('id', '=', $invoiceId)->with('invoice_details')->first();

		if (!empty($invoice)) {

			$itemDiscount = 0;
			$discount = 0;

			foreach ($invoice->invoice_details as $key => $invoiceDetail) {
				$itemDiscount = $itemDiscount + $invoiceDetail->discount;
			}

			$discount = ($itemDiscount > 0 ? 0 : $invoice->total_discount);

			//Get Client Detail
			$client = Clients::where('id', '=', $invoice->client_id)->first();

			//Employees
			$employees = Employees::get();

			$mode = 'edit';
			return view('admin.invoices.form', compact('mode', 'client', 'invoice', 'employees', 'discount'));
		} else {
			$res['status'] = 'danger';
			$res['message'] = 'This invoice number does not exist.';
			$request->session()->flash('res', $res);
			return redirect('admin/invoices');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($invoiceId) {
		$invoice = [
			'total_discount' => 0,
			'grand_total' => 0,
			'advance_payment' => 0,
			'debit_amount' => 0,
			'amount_paid' => 0,
			'on_credit_cash' => 0,
			'on_credit_debit' => 0,
			'payment_history' => NULL,
		];

		$isUpdated = Invoice::where('id', '=', $invoiceId)->update($invoice);

		return $isUpdated;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$invoiceId = $request->invoice_id;

		$this->settleInvoiceDetailById($invoiceId);

		//Delete Invoice Detail
		$isDeleted = Invoice::where('id', '=', $invoiceId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Invoice detail deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/invoices');
		}
	}

	public function settleInvoiceDetailById($invoiceId) {
		$invoice = Invoice::where('id', '=', $invoiceId)->first();

		$client = Clients::where('id', '=', $invoice->client_id)->first();

		//Debit
		if ($invoice->payment_type == 'DEBIT') {
			$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);
			$debitAmount = (float) ($invoice->debit_amount == '' ? 0 : $invoice->debit_amount);
			$totalDebit = $totalDebit - $debitAmount;

			$isUpdated = Clients::where('id', '=', $client->id)->update(['total_debit' => $totalDebit]);
		}

		//Advance Payment
		if ($invoice->payment_type == 'ADVANCE_PAYMENT') {
			$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
			$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);
			$paymentHistory = json_decode($invoice->payment_history);
			$debitAmountPaid = (float) $paymentHistory->debit_amount_paid;
			$advancePaymentCredit = (float) $paymentHistory->advance_payment_credit;
			$debitAmountCredit = (float) $paymentHistory->debit_amount_credit;

			$totalAdvance = $totalAdvance - $advancePaymentCredit;
			$totalDebit = $totalDebit + $debitAmountPaid - $debitAmountCredit;

			//Update Invoice Debit Amount
			if (count($paymentHistory->debit_history) > 0) {
				foreach ($paymentHistory->debit_history as $key => $debitHistory) {
					$invoice1 = Invoice::where('id', '=', $debitHistory->invoice_id)->first();
					$debitAmount = (float) ($invoice1->debit_amount == '' ? 0 : $invoice1->debit_amount);
					$amountPaid = (float) ($invoice1->amount_paid == '' ? 0 : $invoice1->amount_paid);

					$debitAmount = $debitAmount + $debitHistory->debit_amount_paid;
					$amountPaid = $amountPaid - $debitHistory->debit_amount_paid;

					$isUpdated = Invoice::where('id', '=', $debitHistory->invoice_id)->update(['debit_amount' => $debitAmount, 'amount_paid' => $amountPaid]);
				}
			}

			$isUpdated = Clients::where('id', '=', $client->id)->update(['total_advance' => $totalAdvance, 'total_debit' => $totalDebit]);
		}

		//On Credit
		if ($invoice->payment_type == 'ON_CREDIT') {
			$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
			$grandTotal = (float) ($invoice->grand_total == '' ? 0 : $invoice->grand_total);
			$totalAdvance = $totalAdvance + $grandTotal;

			$isUpdated = Clients::where('id', '=', $client->id)->update(['total_advance' => $totalAdvance]);
		}

		//On Credit + Cash
		if ($invoice->payment_type == 'ON_CREDIT+CASH') {
			$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
			$paymentHistory = json_decode($invoice->payment_history);
			$advancePaymentCredit = (float) $paymentHistory->advance_payment_credit;
			$advancePaymentDebit = (float) $paymentHistory->advance_payment_debit;
			$totalAdvance = $totalAdvance + $advancePaymentDebit - $advancePaymentCredit;

			$isUpdated = Clients::where('id', '=', $client->id)->update(['total_advance' => $totalAdvance]);
		}

		//On Credit + Debit
		if ($invoice->payment_type == 'ON_CREDIT+DEBIT') {
			$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
			$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);
			$paymentHistory = json_decode($invoice->payment_history);
			$advancePaymentDebit = (float) $paymentHistory->advance_payment_debit;
			$debitAmountCredit = (float) $paymentHistory->debit_amount_credit;
			$totalAdvance = $totalAdvance + $advancePaymentDebit;
			$totalDebit = $totalDebit - $debitAmountCredit;

			$isUpdated = Clients::where('id', '=', $client->id)->update(['total_advance' => $totalAdvance, 'total_debit' => $totalDebit]);
		}
	}

	public function getInvoiceClients(Request $request) {
		$searchKeyword = $request->keyword;

		$clients = Clients::where('name', 'LIKE', '%' . $searchKeyword . '%')->select('id', 'name', 'phone_number', 'gender', 'dob', 'total_debit', 'total_advance')->get()->toArray();

		return $clients;
	}

	public function getInvoiceItems(Request $request) {
		$invoiceItems = array();

		//Get Services
		$services = Services::select('id', 'name', 'price')->get()->toArray();

		//Get Package
		$packages = Package::select('id', 'name', 'price')->get()->toArray();

		//Get Products
		$products = Products::select('id', 'name', 'price')->get()->toArray();

		foreach ($services as $key => $service) {
			$service['item_type'] = 'SERVICE';
			array_push($invoiceItems, $service);
		}

		foreach ($packages as $key => $package) {
			$package['item_type'] = 'PACKAGE';
			array_push($invoiceItems, $package);
		}

		foreach ($products as $key => $product) {
			$product['item_type'] = 'PRODUCT';
			array_push($invoiceItems, $product);
		}

		return $invoiceItems;
	}

	public function createInvoiceByRunningService($clientId, $mode) {
		if ($mode == 'save') {

			//Settle Debit Amount
			if (!empty(RequestFilter::query('is_settle_debit'))) {
				$settleDebitAmount = (float) (RequestFilter::query('settle_debit_amount') == '' ? 0 : RequestFilter::query('settle_debit_amount'));
				$data = app('App\Http\Controllers\ClientsController')->settleDebitAmountByClientId($clientId, $settleDebitAmount);
			}

			//Advance Payment
			if (!empty(RequestFilter::query('is_advance_payment'))) {
				$clientAdvancePayment = (float) (RequestFilter::query('client_advance_payment') == '' ? 0 : RequestFilter::query('client_advance_payment'));
				$client_data = Clients::where('id', '=', $clientId)->first();
				$totalAdvance = (float) ($client_data->total_advance == '' ? 0 : $client_data->total_advance);
				$totalAdvance = $totalAdvance + $clientAdvancePayment;
				$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance]);
			}

			//Get Client Detail
			$client = Clients::where('id', '=', $clientId)->first();

			//Get Client Running Services
			$running_services = RunningServices::where('client_id', '=', $clientId)->get();

			if (count($running_services) > 0) {
				//Generate Unique Quote Number EX. 1 TO 0001
				$invoiceCount = Invoice::count();

				$invoiceNumber = trim(sprintf('%04u', $invoiceCount + 1));

				$paymentType = RequestFilter::query('payment_type');

				//Save Invoice
				$invoiceArr = [
					'user_id' => Auth::user()->id,
					'client_id' => $clientId,
					'invoice_number' => $invoiceNumber,
					'bill_date' => Carbon::now()->format('Y-m-d'),
					'payment_type' => $paymentType,
					'created_at' => Carbon::now(),
				];

				$invoiceId = Invoice::insertGetId($invoiceArr);

				$grandTotal = 0;

				$totalItemDiscount = 0;

				foreach ($running_services as $key => $running_service) {

					$totalItemDiscount = $totalItemDiscount + $running_service->discount;
					$grandTotal = $grandTotal + $running_service->total_price;

					//Save Invoice Detail
					$invoiceDetailArr = [
						'invoice_id' => $invoiceId,
						'item_id' => $running_service->item_id,
						'name' => $running_service->name,
						'beautician_id' => $running_service->beautician_id,
						'quantity' => $running_service->quantity,
						'discount' => $running_service->discount,
						'price' => $running_service->price,
						'total_price' => $running_service->total_price,
						'item_type' => $running_service->item_type,
						'created_at' => Carbon::now(),
					];

					$invoiceDetailId = InvoiceDetails::insertGetId($invoiceDetailArr);
				}

				$notes = RequestFilter::query('notes');

				//Update Grand Total
				$newInvoiceArr = [
					'grand_total' => $grandTotal,
					'notes' => $notes,
				];

				//If payment type DEBIT save Debit Amount Or Amount Paid
				if ($paymentType == 'DEBIT') {
					$debitAmount = (float) (RequestFilter::query('debit_amount') == '' ? 0 : RequestFilter::query('debit_amount'));
					$amountPaid = (float) (RequestFilter::query('amount_paid') == '' ? 0 : RequestFilter::query('amount_paid'));
					$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);

					$newInvoiceArr['debit_amount'] = $debitAmount;
					$newInvoiceArr['amount_paid'] = $amountPaid;

					// Update total debit amount in client table
					$totalDebit = $totalDebit + $debitAmount;
					$isUpdated = Clients::where('id', '=', $clientId)->update(['total_debit' => $totalDebit]);
				}

				//If payment type ADVANCE PAYMENT save Advance Payment Or Debit Amount
				else if ($paymentType == 'ADVANCE_PAYMENT') {
					$advancePayment = (float) (RequestFilter::query('advance_payment') == '' ? 0 : RequestFilter::query('advance_payment'));
					$amountPaid = (float) (RequestFilter::query('amount_paid') == '' ? 0 : RequestFilter::query('amount_paid'));
					$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
					$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);

					$paymentHistory = array();
					if ($advancePayment >= $totalDebit) {
						$advancePayment = $advancePayment - $totalDebit;
						$data = app('App\Http\Controllers\ClientsController')->settleDebitAmountByClientId($clientId, $totalDebit);
						$paymentHistory['debit_amount_paid'] = $totalDebit;
						$paymentHistory['debit_history'] = $data['debit_history'];
					} else {
						$data = app('App\Http\Controllers\ClientsController')->settleDebitAmountByClientId($clientId, $advancePayment);
						$paymentHistory['debit_amount_paid'] = $advancePayment;
						$paymentHistory['debit_history'] = $data['debit_history'];
						$advancePayment = 0;
					}

					if ($advancePayment >= $grandTotal) {
						// Update total debit amount in client table
						$totalAdvance = $totalAdvance + $advancePayment - $grandTotal;
						$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance]);

						$newInvoiceArr['advance_payment'] = $advancePayment - $grandTotal;
						$newInvoiceArr['amount_paid'] = $amountPaid;
						$newInvoiceArr['debit_amount'] = 0;
						$paymentHistory['advance_payment_credit'] = $advancePayment - $grandTotal;
						$paymentHistory['debit_amount_credit'] = 0;
					} else {
						//Get Client Detail
						$client = Clients::where('id', '=', $clientId)->first();
						$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);

						$debitAmount = $grandTotal - $advancePayment;
						// Update total debit amount in client table
						$totalDebit = $totalDebit + $debitAmount;
						$isUpdated = Clients::where('id', '=', $clientId)->update(['total_debit' => $totalDebit]);

						$newInvoiceArr['advance_payment'] = 0;
						$newInvoiceArr['amount_paid'] = $amountPaid;
						$newInvoiceArr['debit_amount'] = $debitAmount;
						$paymentHistory['advance_payment_credit'] = 0;
						$paymentHistory['debit_amount_credit'] = $debitAmount;
					}

					$newInvoiceArr['payment_history'] = json_encode($paymentHistory);
				}

				//If payment type ON CREDIT or ON CREDIT + CASH save amount paid
				else if ($paymentType == 'ON_CREDIT' || $paymentType == 'ON_CREDIT+CASH') {
					$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
					$amountPaid = (float) (RequestFilter::query('amount_paid') == '' ? 0 : RequestFilter::query('amount_paid'));
					$newInvoiceArr['amount_paid'] = $amountPaid;

					if ($paymentType == 'ON_CREDIT+CASH') {
						$onCreditCash = (float) (RequestFilter::query('on_credit_cash') == '' ? 0 : RequestFilter::query('on_credit_cash'));
						$onCreditDueCash = (float) (RequestFilter::query('on_credit_due_cash') == '' ? 0 : RequestFilter::query('on_credit_due_cash'));
						$advanceCash = $onCreditCash - $onCreditDueCash;
						$totalAdvance = $totalAdvance - ($grandTotal - $onCreditDueCash);

						$paymentHistory = array();
						$paymentHistory['on_credit_due_cash'] = $onCreditDueCash;
						$paymentHistory['advance_payment_credit'] = $advanceCash;
						$paymentHistory['advance_payment_debit'] = $grandTotal - $onCreditDueCash;

						if ($advanceCash > 0) {
							$totalAdvance = $totalAdvance + $advanceCash;
						}
						$newInvoiceArr['on_credit_cash'] = $onCreditCash;
						$newInvoiceArr['payment_history'] = json_encode($paymentHistory);
					} else {
						$totalAdvance = $totalAdvance - $grandTotal;
					}
					// Update remain total advance
					$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance]);

				} else if ($paymentType == 'ON_CREDIT+DEBIT') {
					$totalAdvance = (float) ($client->total_advance == '' ? 0 : $client->total_advance);
					$amountPaid = (float) (RequestFilter::query('amount_paid') == '' ? 0 : RequestFilter::query('amount_paid'));
					$onCreditDebit = (float) (RequestFilter::query('on_credit_debit') == '' ? 0 : RequestFilter::query('on_credit_debit'));
					$onCreditDueDebit = (float) (RequestFilter::query('on_credit_due_debit') == '' ? 0 : RequestFilter::query('on_credit_due_debit'));
					$totalDebit = (float) ($client->total_debit == '' ? 0 : $client->total_debit);
					$totalAdvance = $totalAdvance - ($grandTotal - $onCreditDueDebit);

					$paymentHistory = array();
					$paymentHistory['on_credit_due_debit'] = $onCreditDueDebit;
					$paymentHistory['debit_amount_credit'] = $onCreditDebit;
					$paymentHistory['advance_payment_debit'] = $grandTotal - $onCreditDueDebit;

					$newInvoiceArr['amount_paid'] = $amountPaid;
					$newInvoiceArr['on_credit_debit'] = $onCreditDebit;
					$newInvoiceArr['payment_history'] = json_encode($paymentHistory);

					// Update remain total advance and total debit
					$totalDebit = $totalDebit + $onCreditDebit;
					$isUpdated = Clients::where('id', '=', $clientId)->update(['total_advance' => $totalAdvance, 'total_debit' => $totalDebit]);
				} else {
					$totalDiscount = (float) (RequestFilter::query('discount') == '' ? 0 : RequestFilter::query('discount'));

					if ($totalDiscount > 0) {
						$newInvoiceArr['grand_total'] = $grandTotal - $totalDiscount;
						$newInvoiceArr['amount_paid'] = $grandTotal - $totalDiscount;
						$newInvoiceArr['total_discount'] = $totalDiscount;
					} else {
						$newInvoiceArr['amount_paid'] = $grandTotal;
						if ($totalItemDiscount > 0) {
							$newInvoiceArr['total_discount'] = $totalItemDiscount;
						}
					}
				}

				//Settle Debit Amount
				if (!empty(RequestFilter::query('is_settle_debit'))) {
					$settleDebitAmount = (float) (RequestFilter::query('settle_debit_amount') == '' ? 0 : RequestFilter::query('settle_debit_amount'));
					$newInvoiceArr['settle_debit_amount'] = $settleDebitAmount;
					$newInvoiceArr['amount_paid'] = $newInvoiceArr['amount_paid'] + $settleDebitAmount;
					$newInvoiceArr['notes'] = ($newInvoiceArr['notes'] == '' ? 'Invoice + Settle Debit' : $newInvoiceArr['notes'] . ', Invoice + Settle Debit');
				}

				//Advance Payment
				if (!empty(RequestFilter::query('is_advance_payment'))) {
					$clientAdvancePayment = (float) (RequestFilter::query('client_advance_payment') == '' ? 0 : RequestFilter::query('client_advance_payment'));
					$newInvoiceArr['client_advance_payment'] = $clientAdvancePayment;
					$newInvoiceArr['amount_paid'] = $newInvoiceArr['amount_paid'] + $clientAdvancePayment;
				}

				$isUpdated = Invoice::where('id', '=', $invoiceId)->update($newInvoiceArr);
			}
		}

		$isUpdated = Clients::where('id', '=', $clientId)->update(['service' => false]);
		$isDeleted = RunningServices::where('client_id', '=', $clientId)->delete();

		return $isDeleted;
	}

	public function changeInvoiceBillingDate(Request $request) {
		$invoiceId = $request->invoice_id;

		//Update Invoice Billing Date
		$isUpdated = Invoice::where('id', '=', $invoiceId)->update(['bill_date' => Carbon::parse($request->billing_date)->format('Y-m-d')]);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Invoice billing date edited successfully!';
		} else {
			$res['status'] = 'fail';
			$res['message'] = 'Error!';
		}

		$request->session()->flash('res', $res);
		return redirect('/admin/invoices');
	}
}
