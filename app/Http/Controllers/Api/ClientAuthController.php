<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Client_role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class ClientAuthController extends Controller
{
	public $successStatus = 200;

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$clients = Client::with('roles')->get();
		return response()->json($clients, $this->successStatus);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		$validator = Validator::make($data, [
			'username' => 'required|unique:clients',
			'email' => 'required|email|unique:clients',
			'password' => 'required',
			'client_role' => 'required|exists:client_roles,role_name'
		])->validate();
		// if ($validator->fails()) {
		// 	return response()->json($validator->errors(), 401);
		// }

		$client_data = $data;
		unset($client_data['client_role']);
		$client_data['password'] = Hash::make($client_data['password']);
		try {
			DB::beginTransaction();
			$client = Client::create($client_data);
			$client_role = Client_role::where('role_name', $data['client_role'])->first();
			$client->roles()->attach($client_role->client_role_id);
			DB::commit();

			$created_client = Client::with('roles')->find($client->client_id);
			return response()->json($created_client, $this->successStatus);
		} catch (\Throwable $th) {
			DB::rollBack();
			return response()->json(['error' => $th->getMessage()]);
		}
	}

	public function login(Request $request)
	{
		$data = $request->all();
		$client = [];
		try {
			$validator = Validator::make($data, [
				'username' => 'required|exists:clients,username',
				'password' => 'required',
			], [
				'username.exists' => 'Invalid credentials'
				]);
			if ($validator->fails()) return response()->json(['error' => $validator->errors()]);
			// ? get client
			$client = Client::with('roles')->where('username', $data['username'])->firstOrFail();

			if (!Hash::check($data['password'], $client->password)) throw new \Exception("Invalid credentials");
		} catch (\Throwable $th) {
			return response()->json(['error' => $th->getMessage()]);
		}
		return response()->json(['success' => $client], $this->successStatus);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$client = Client::with('roles')->find($id);
		return response()->json($client, $this->successStatus);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		try {
			if (empty($id)) throw new \Exception("Missing client_id");
			$inputs = $request->all();
			$client = Client::with('roles')->findOrFail($id);
			foreach ($inputs as $key => $value) {
				switch ($key) {
					case 'password':
						$client->$key = Hash::make($value);
						break;
					case 'email':
						$client->$key = $value;
						$client->email_verified_at = null;
						break;
					case 'client_role':
						break;
					default:
						if (!Schema::hasColumn('clients', $key)) break;
						$client->$key = $value;
						break;
				}
			}
			$client->save();
			return response()->json(['success' => $client], $this->successStatus);
		} catch (\Throwable $th) {
			$error_res = '';
			switch (get_class($th)) {
				case 'Illuminate\Database\QueryException':
					$error_res = explode(' for key', $th->errorInfo[2])[0];
					break;
				case 'Illuminate\Database\Eloquent\ModelNotFoundException':
					$error_res = 'No record found';
					break;
				default:
					$error_res = $th->getMessage();
					break;
			}
			return response()->json([
				'error' => $error_res,
			]);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		try {
			$client = Client::findOrFail($id);
			$client->delete();
			return response()->json(['success' => 'Record deleted successfully'], $this->successStatus);
		} catch (\Throwable $th) {
			$error_res = '';
			switch (get_class($th)) {
				case 'Illuminate\Database\Eloquent\ModelNotFoundException':
					$error_res = 'No record found';
					break;
				default:
					$error_res = $th->getMessage();
					break;
			}
			return response()->json([
				'error' => $error_res,
			]);
		}
	}
}
