<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientServices extends Model {
	protected $table = 'client_services';

	public function client() {
		return $this->belongsTo('App\Models\Clients', 'client_id', 'id');
	}
}
