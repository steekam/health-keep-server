<?php

use Illuminate\Database\Seeder;

class ClientRolesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('client_roles')->insert([
			'role_name' => 'patient',
			'created_at' => now()
		]);
		DB::table('client_roles')->insert([
			'role_name' => 'doctor',
			'created_at' => now()
		]);
	}
}
