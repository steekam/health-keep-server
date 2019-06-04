<?php

use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		factory(App\Models\Client::class, 5)->create()->each( function ($client) {
			$role = App\Models\Client_role::all()->random(1);
			$client->roles()->attach($role);
		});
	}
}
