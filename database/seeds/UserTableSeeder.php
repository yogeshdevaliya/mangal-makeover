<?php

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$userData = [
			'name' => 'Super Admin',
			'email' => 'admin@gmail.com',
			'password' => bcrypt('123123'),
			'created_at' => Carbon::now(),
		];

		$userId = User::insertGetId($userData);

		$roleAdmin = Role::where('name', 'super_admin')->first();

		$user = User::where('id', $userId)->first();
		$user->attachRole($roleAdmin);
	}
}
