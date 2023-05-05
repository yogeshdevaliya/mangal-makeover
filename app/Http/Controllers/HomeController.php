<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Employees;
use App\Models\RunningServices;
use App\Models\Services;
use Auth;
use Carbon\Carbon;

class HomeController extends Controller {
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index() {
		if (Auth::check()) {
			return redirect('/dashboard');
		} else {
			return view('home');
		}
	}
	public function dashboard() {
		$all_clients = Clients::select('id', 'name', 'phone_number', 'service', 'total_debit', 'total_advance', 'dob', 'anniversary')->get();
		// return $all_clients;
		// $clients = Clients::where('service', '=', 1)->select('id', 'name', 'phone_number')->get();
		$services = Services::get();
		$employees = Employees::get();

		$todayDate = Carbon::now()->format('d-m');

		$birthdays = [];
		$anniversaries = [];

		foreach ($all_clients as $key => $client) {

			// Running services
			if ($client->service == 1) {
				$running_services = RunningServices::where('client_id', '=', $client->id)->get();
				foreach ($running_services as $key1 => $running_service) {
					$beauticianIds = explode(',', $running_service->beautician_id);
					$beauticianNames = array();
					foreach ($beauticianIds as $beauticianId) {
						$beauticianName = Employees::where('id', '=', $beauticianId)->pluck('name')->first();
						array_push($beauticianNames, $beauticianName);
					}
					$running_services[$key1]['beautician_name'] = implode(',', $beauticianNames);
				}
				$client['running_services'] = $running_services;
			}

			if (!empty($client->dob)) {
				//Check client dob
				$dob = Carbon::parse($client->dob)->format('d-m');

				if ($dob == $todayDate) {
					array_push($birthdays, $client);
				}
			}

			if (!empty($client->anniversary)) {
				//Check client anniversary
				$anniversary = Carbon::parse($client->anniversary)->format('d-m');
				if ($anniversary == $todayDate) {
					array_push($anniversaries, $client);
				}
			}
		}

		// foreach ($clients as $key => $value) {
		// 	$running_services = RunningServices::where('client_id', '=', $value->id)->get();

		// 	foreach ($running_services as $key1 => $running_service) {
		// 		$beauticianIds = explode(',', $running_service->beautician_id);
		// 		$beauticianNames = array();

		// 		foreach ($beauticianIds as $beauticianId) {
		// 			$beauticianName = Employees::where('id', '=', $beauticianId)->pluck('name')->first();
		// 			array_push($beauticianNames, $beauticianName);
		// 		}

		// 		$running_services[$key1]['beautician_name'] = implode(',', $beauticianNames);
		// 	}
		// 	$clients[$key]['running_services'] = $running_services;
		// }

		return view('dashboard', compact('employees', 'services', 'all_clients', 'birthdays', 'anniversaries'));
	}
}
