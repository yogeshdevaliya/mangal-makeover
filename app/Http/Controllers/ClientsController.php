<?php

namespace App\Http\Controllers;

//Models
use App\Models\Clients;
use App\Models\ClientServices;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\RunningServices;
use App\Models\Services;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientsController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {

		//Get Clients
		$clients = Clients::paginate(10);

		foreach ($clients as $key => $client) {
			$package = 'NA';

			//Get Client Package
			if ($client->package_ids != '') {
				$packageIds = explode(',', $client->package_ids);
				$packageNames = Package::whereIn('id', $packageIds)->get()->pluck('name')->toArray();
				$package = implode(',', $packageNames);
			}
			$clients[$key]['package'] = $package;
		}

		//Get Package
		$packages = Package::get();

		$mode = 'add';

		return view('admin.clients.list', compact('clients', 'mode', 'packages'));
	}

	public function getClientData(Request $request)
	{
		if( isset($request->type) && $request->type == 'search' ) {
			$term = $request->term;
			$clients = Clients::where('name', 'LIKE', '%'.$term.'%')
				->orWhere('phone_number', 'LIKE', '%'.$term.'%')
				->orWhere('dob', 'LIKE', '%'.$term.'%')->paginate(10);
		} else {
			$clients = Clients::paginate(10);
		}
		return response()->json($clients);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//Get Package
		$packages = Package::get();

		$mode = 'add';

		return view('admin.clients.form', compact('packages', 'mode'));
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
			'client_name' => ['required'],
			'phone_number' => ['required', 'unique:clients'],
			'gender' => ['required'],
			// 'package' => ['required'],
		]);

		//Save Clients
		$client = [
			'name' => $request->client_name,
			// 'slug' => str_slug($request->client_name, '-'),
			'phone_number' => $request->phone_number,
			'gender' => $request->gender,
			'email' => $request->email,
			'address' => $request->address,
			'dob' => ($request->birthdate == '' ? NULL : Carbon::parse($request->birthdate)->format('Y-m-d')),
			'anniversary' => ($request->anniversary == '' ? NULL : Carbon::parse($request->anniversary)->format('Y-m-d')),
			'package_ids' => (empty($request->package) ? NULL : implode(',', $request->package)),
			'created_at' => Carbon::now(),
		];

		$isSaved = Clients::insertGetId($client);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Client added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/clients');
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
	public function edit($clientId) {
		//Get Client
		$client = Clients::where('id', '=', $clientId)->first();

		$clientPackage = array();

		//Get Client Package
		if ($client->package_ids != '') {
			$clientPackage = explode(',', $client->package_ids);
		}

		//Get Clients
		$clients = Clients::get();

		foreach ($clients as $key => $client_package) {
			$package = 'NA';

			//Get Client Package
			if ($client_package->package_ids != '') {
				$packageIds = explode(',', $client_package->package_ids);
				$packageNames = Package::whereIn('id', $packageIds)->get()->pluck('name')->toArray();

				$package = implode(',', $packageNames);
			}

			$clients[$key]['package'] = $package;
		}

		//Get Package
		$packages = Package::get();

		$mode = 'edit';

		return view('admin.clients.list', compact('clients', 'mode', 'packages', 'clientPackage', 'client'));
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
			'client_name' => ['required'],
			'phone_number' => ['required'],
			'gender' => ['required'],
			// 'package' => ['required'],
		]);

		$clientId = $request->client_id;

		$client = Clients::where('id', '=', $clientId)->first();

		//If phone number different check mobile is exist using validator
		if ($client->phone_number != $request->phone_number) {
			$request->validate([
				'phone_number' => ['unique:clients'],
			]);
		}

		//Update Clients
		$clientArr = [
			'name' => $request->client_name,
			// 'slug' => str_slug($request->client_name, '-'),
			'phone_number' => $request->phone_number,
			'gender' => $request->gender,
			'email' => $request->email,
			'address' => $request->address,
			'dob' => ($request->birthdate == '' ? NULL : Carbon::parse($request->birthdate)->format('Y-m-d')),
			'anniversary' => ($request->anniversary == '' ? NULL : Carbon::parse($request->anniversary)->format('Y-m-d')),
			'package_ids' => (empty($request->package) ? NULL : implode(',', $request->package)),
			'updated_at' => Carbon::now(),
		];

		$isUpdated = Clients::where('id', '=', $clientId)->update($clientArr);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Client updated successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/clients');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$clientId = $request->client_id;

		//Delete Client
		$isDeleted = Clients::where('id', '=', $clientId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Client deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/clients');
		}
	}

	public function clientServicesList() {

		//Get Client Services
		$clientServices = ClientServices::with('client')->get();

		//Get Service Name
		foreach ($clientServices as $key => $clientService) {
			$serviceIds = explode(',', $clientService->service_ids);
			$serviceNameArr = Services::whereIn('id', $serviceIds)->get()->pluck('name')->toArray();
			$clientServices[$key]->service = implode(',', $serviceNameArr);
		}

		//Get Services
		$services = Services::get();

		//Get Clients
		$clients = Clients::get();

		$mode = 'add';

		return view('admin.clients.services.list', compact('clientServices', 'services', 'clients', 'mode'));
	}

	public function clientServicesAdd(Request $request) {
		//Validate form
		$request->validate([
			'client' => ['required'],
			'service' => ['required'],
			'service_date' => ['required'],
		]);

		//Add Client Service
		$clientService = [
			'user_id' => Auth::user()->id,
			'client_id' => $request->client,
			'service_ids' => (empty($request->service) ? NULL : implode(',', $request->service)),
			'service_date' => ($request->service_date == '' ? NULL : Carbon::parse($request->service_date)->format('Y-m-d')),
			'created_at' => Carbon::now(),
		];

		$servicePriceArr = Services::whereIn('id', $request->service)->get()->pluck('price');

		$grandTotal = 0;
		foreach ($servicePriceArr as $servicePrice) {
			$servicePrice = (float) $servicePrice;
			$grandTotal = $grandTotal + $servicePrice;
		}

		$clientService['grand_total'] = number_format($grandTotal, 2);

		$isSaved = ClientServices::insertGetId($clientService);

		if ($isSaved) {
			$res['status'] = 'success';
			$res['message'] = 'Client service added successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/client/services');
		}
	}

	public function clientServicesDestroy(Request $request) {

		$clientServiceId = $request->client_service_id;

		//Delete Client Service
		$isDeleted = ClientServices::where('id', '=', $clientServiceId)->delete();

		if ($isDeleted) {
			$res['status'] = 'danger';
			$res['message'] = 'Client service deleted successfully.';
			$request->session()->flash('res', $res);
			return redirect('admin/client/services');
		}
	}

	public function startService(Request $request, $clientId) {

		$isUpdated = Clients::where('id', '=', $clientId)->update(['service' => true]);

		//Delete All Running Service
		$isDeleted = RunningServices::where('client_id', '=', $clientId)->delete();

		// Add services
		$clientServices = $request->input('client_services');

		if (!empty($clientServices)) {
			$clientServices = array_values($clientServices);

			foreach ($clientServices as $key => $clientService) {
				$serviceArr = [
					'client_id' => $clientId,
					'item_id' => (float) ($clientService['item_id'] == '' ? 0 : $clientService['item_id']),
					'name' => $clientService['name'],
					'beautician_id' => implode(',', $clientService['beautician_id']),
					'quantity' => (float) $clientService['quantity'],
					'discount' => (float) ($clientService['discount'] == '' ? 0 : $clientService['discount']),
					'price' => (float) $clientService['price'],
					'total_price' => (float) $clientService['total_price'],
					'item_type' => ($clientService['item_type'] == '' ? 'SERVICE' : $clientService['item_type']),
					'created_at' => Carbon::now(),
				];

				$runningServiceId = RunningServices::insertGetId($serviceArr);
			}
		}

		if ($isUpdated) {
			$res['status'] = 'success';
			if ($isDeleted) {
				$res['message'] = 'Client service updated!';
			} else {
				$res['message'] = 'Client service started!';
			}
		} else {
			$res['status'] = 'fail';
			$res['message'] = 'Error!';
		}
		$request->session()->flash('res', $res);
		return redirect('dashboard');
	}

	public function endService(Request $request, $clientId) {
		$isUpdated = Clients::where('id', '=', $clientId)->update(['service' => false]);
		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Client service end!';
		} else {
			$res['status'] = 'fail';
			$res['message'] = 'Error!';
		}
		$request->session()->flash('res', $res);
		return redirect('dashboard');
	}

	public function clientSettleDebitAmount(Request $request) {
		$clientId = $request->client_id;
		$debitAmount = (float) $request->debit_amount;

		$data = $this->settleDebitAmountByClientId($clientId, $debitAmount, 1);

		if ($data['is_updated']) {
			$res['status'] = 'success';
			$res['message'] = 'Settle debit amount successfully.';
		} else {
			$res['status'] = 'fail';
			$res['message'] = 'Error!';
		}

		$request->session()->flash('res', $res);
		return redirect()->back();
	}

	public function settleDebitAmountByClientId($clientId, $debitAmount, $isInvoiceDebitAmount = 0) {
		$client = Clients::where('id', '=', $clientId)->first();

		$clientTotalDebit = (float) $client->total_debit;
		$finalTotalDebit = $clientTotalDebit - $debitAmount;

		$isUpdated = Clients::where('id', '=', $clientId)->update(['total_debit' => $finalTotalDebit]);

		$debitHistory = array();

		if ($isInvoiceDebitAmount == 1) {

			$invoiceCount = Invoice::count();
			$invoiceNumber = trim(sprintf('%04u', $invoiceCount + 1));

			$invoiceArr = [
				'user_id' => Auth::user()->id,
				'client_id' => $clientId,
				'invoice_number' => $invoiceNumber,
				'bill_date' => Carbon::now()->format('Y-m-d'),
				'grand_total' => $debitAmount,
				'amount_paid' => $debitAmount,
				'notes' => 'Settle Debit',
				'payment_type' => 'CASH',
				'is_settle_debit' => 1,
				'created_at' => Carbon::now(),
			];

			$invoiceId = Invoice::insertGetId($invoiceArr);

			// $invoices = Invoice::where('client_id', '=', $clientId)->where('payment_type', '=', 'DEBIT')->with('invoice_details')->get();

			// $isAmountDebited = 0;

			// //Update Invoice debit amount
			// if (!empty($invoices)) {
			// 	foreach ($invoices as $key => $invoice) {
			// 		$invoiceDebitAmount = (float) ($invoice->debit_amount == '' ? 0 : $invoice->debit_amount);
			// 		$invoiceAmountPaid = (float) ($invoice->amount_paid == '' ? 0 : $invoice->amount_paid);

			// 		if ($debitAmount == $clientTotalDebit) {
			// 			$invoiceAmountPaid = $invoiceAmountPaid + $invoiceDebitAmount;
			// 			$invoiceDebitAmount = 0;
			// 		} else {
			// 			if ($isAmountDebited == 0) {
			// 				if ($invoiceDebitAmount == $debitAmount) {
			// 					$invoiceAmountPaid = $invoiceAmountPaid + $invoiceDebitAmount;
			// 					$invoiceDebitAmount = 0;
			// 					$debitAmount = 0;
			// 				} else {
			// 					if ($debitAmount < $invoiceDebitAmount) {
			// 						$invoiceDebitAmount = $invoiceDebitAmount - $debitAmount;
			// 						$invoiceAmountPaid = $invoiceAmountPaid + $debitAmount;
			// 						$debitAmount = 0;
			// 					}
			// 					if ($debitAmount > $invoiceDebitAmount) {
			// 						$debitAmount = $debitAmount - $invoiceDebitAmount;
			// 						$invoiceAmountPaid = $invoiceAmountPaid + $invoiceDebitAmount;
			// 						$invoiceDebitAmount = 0;
			// 					}
			// 				}
			// 			}
			// 		}

			// 		if ($isAmountDebited == 0) {
			// 			$currentAmountPaid = (float) ($invoice->amount_paid == '' ? 0 : $invoice->amount_paid);
			// 			$invoiceArr = [
			// 				'debit_amount' => $invoiceDebitAmount,
			// 				'amount_paid' => $invoiceAmountPaid,
			// 				'updated_at' => Carbon::now(),
			// 			];

			// 			$debitHistoryArr = array();
			// 			$debitHistoryArr['invoice_id'] = $invoice->id;
			// 			$debitHistoryArr['debit_amount_paid'] = $invoiceAmountPaid - $currentAmountPaid;
			// 			array_push($debitHistory, $debitHistoryArr);

			// 			$isInvoiceUpdated = Invoice::where('id', '=', $invoice->id)->update($invoiceArr);
			// 		}

			// 		if ($debitAmount <= 0) {
			// 			$isAmountDebited = 1;
			// 		}
			// 	}
			// }
		}

		$data = [
			'is_updated' => $isUpdated,
			'debit_history' => $debitHistory,
		];
		return $data;
	}

	public function checkClientPhoneNumber(Request $request) {
		$client = Clients::where('phone_number', '=', $request->phone_number)->first();

		if (!empty($client)) {
			$status = 0;
		} else {
			$status = 1;
		}

		return $status;
	}

	public function clientRunningServiceDelete(Request $request) {

		$clientId = $request->client_id;

		//Update Service Status
		$isUpdated = Clients::where('id', '=', $clientId)->update(['service' => false]);

		//Delete Running Services
		$isDeleted = RunningServices::where('client_id', '=', $clientId)->delete();

		if ($isDeleted) {
			$res['status'] = 'success';
			$res['message'] = 'Client service deleted!';
		} else {
			$res['status'] = 'fail';
			$res['message'] = 'Error!';
		}

		$request->session()->flash('res', $res);
		return redirect('/dashboard');
	}

	public function getClientRunningServices(Request $request) {
		$clientId = $request->client_id;

		$runningServices = RunningServices::where('client_id', '=', $clientId)->get();

		foreach ($runningServices as $key => $runningService) {
			$discount = (float) $runningService->discount;
			$totalPrice = (float) $runningService->total_price;

			$totalPrice = $totalPrice + $discount;

			$runningServicesArr = [
				'discount' => 0,
				'total_price' => $totalPrice,
				'updated_at' => Carbon::now(),
			];

			$isUpdated = RunningServices::where('id', '=', $runningService->id)->update($runningServicesArr);
		}

		return $runningServices;
	}

	public function postResetClientDetail(Request $request) {

		$clientId = $request->client_id;

		//Deleted Invoice And Invoice Detail
		$isDeleted = Invoice::where('client_id', '=', $clientId)->delete();

		//Update Total Debit And Total Advance
		$isUpdated = Clients::where('id', '=', $clientId)->update(['total_debit' => 0, 'total_advance' => 0]);

		if ($isUpdated) {
			$res['status'] = 'success';
			$res['message'] = 'Client detail reset successfully!';
		} else {
			$res['status'] = 'fail';
			$res['message'] = 'Error!';
		}

		$request->session()->flash('res', $res);
		return redirect('/admin/clients');
	}
}
