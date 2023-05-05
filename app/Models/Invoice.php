<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
	protected $table = 'invoice';

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	public function client() {
		return $this->belongsTo('App\Models\Clients', 'client_id', 'id');
	}

	public function invoice_details() {
		return $this->hasMany('App\Models\InvoiceDetails', 'invoice_id', 'id');
	}

	public function getBillDateAttribute($value)
	{
		return Carbon::parse($value)->format('d-m-Y');
	}
}
