<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table('roles')->insert([
			[
				'name' => 'super_admin',
				'display_name' => 'SUPER ADMIN',
				'description' => 'Super Admin can manage overall system and users.',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			],
			[
				'name' => 'admin',
				'display_name' => 'ADMIN',
				'description' => 'Admin can manage overall system and users based on assigned permissions.',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			],
			[
				'name' => 'employee',
				'display_name' => 'Employee',
				'description' => 'Employees has access to invoicing.',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			],
		]);
	}
}
