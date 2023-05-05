<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model {
	protected $table = 'services';

	public function category() {
		return $this->belongsTo('App\Models\ServiceCategory', 'service_category_id', 'id');
	}

	public function service() {
		return $this->belongsTo('App\Models\Services', 'service_id', 'id');
	}
}
