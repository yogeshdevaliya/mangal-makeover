<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table = 'clients';
    
	public function employee()
	{
		return $this->belongsTo('App\Models\Employees', 'employee_id');	
	}
}
