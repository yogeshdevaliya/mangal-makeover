<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model {
	protected $table = 'expenses';

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
}
