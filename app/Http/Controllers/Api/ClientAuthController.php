<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Client_role;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ClientAuthController extends Controller
{
	public $successStatus = 200;
	public $errorStatus = 422;

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$clients = Client::with('roles')->get();
		return response()->json($clients, $this->successStatus);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		Validator::make($data, [
			'username' => 'required|unique:clients',
			'email' => 'required|email|unique:clients',
			'password' => 'required',
			'client_role' => 'required|exists:client_roles,role_name'
		])->validate();

		$client_data = $data;
		unset($client_data['client_role']);
		$client_data['password'] = Hash::make($client_data['password']);
		try {
			DB::beginTransaction();
			$client = Client::create($client_data);
			$client_role = Client_role::where('role_name', $data['client_role'])->first();
			$client->roles()->attach($client_role->client_role_id);

			//? Send verification email
			$client->sendEmailVerificationNotification();
			DB::commit();

			$created_client = Client::with('roles')->find($client->client_id);
			return response()->json($created_client, $this->successStatus);
		} catch (Throwable $th) {
			DB::rollBack();
			return response()->json(['error' => $th->getMessage()], $this->errorStatus);
		}
	}

	public function login(Request $request)
	{
		$data = $request->all();
		$client = [];
		Validator::make($data, [
			'username' => 'required|exists:clients,username',
			'password' => 'required',
		], [
			'username.exists' => 'Invalid credentials'
		])->validate();
		try {
			// ? get client
			$client = Client::with('roles')->where('username', $data['username'])->firstOrFail();
			if (!Hash::check($data['password'], $client->password)) throw new Exception("Invalid credentials");
		} catch (Throwable $th) {
			return response()->json(['error' => $th->getMessage()], $this->errorStatus);
		}
		return response()->json($client);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function show($id)
	{
		$client = Client::with('roles')->find($id);
		return response()->json($client, $this->successStatus);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		try {
			if (empty($id)) throw new Exception("Missing client_id");
			$inputs = $request->all();
			$client = Client::with('roles')->findOrFail($id);
			foreach ($inputs as $key => $value) {
				switch ($key) {
					case 'email':
						$client->$key = $value;
						//? temporary disable un-verification for email change
//						$client->email_verified_at = null;
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
			return response()->json($client, $this->successStatus);
		} catch (Throwable $th) {
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
			], $this->errorStatus);
		}
	}

	/**
	 * Update client's password
	 * @param Request $request
	 * @param $id
	 * @return JsonResponse
	 */
	public function passwordReset(Request $request, $id)
	{
		try {
			if (empty($id)) throw new Exception("Missing client_id");
			$client = Client::with('roles')->findOrFail($id);
		} catch (Throwable $throwable) {
			if ($throwable instanceof ModelNotFoundException) {
				$error_res = 'No record found';
			} else {
				$error_res = $throwable->getMessage();
			}
			return response()->json([
				'error' => $error_res,
			], $this->errorStatus);
		}
		$data = $request->all();
		Validator::make($data, [
			'old_password' => 'required|old_password:' . $client->getAuthPassword(),
			'new_password' => 'required',
		], [
			'old_password' => 'Invalid old password'
		])->validate();
		try {
			$client->password = Hash::make($data['new_password']);
			$client->save();
		} catch (Throwable $throwable) {
			$error_res = $throwable->getMessage();
			return response()->json([
				'error' => $error_res,
			], $this->errorStatus);
		}
		return response()->json($client);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$client = Client::findOrFail($id);
			$client->delete();
			return response()->json(['success' => 'Record deleted successfully'], $this->successStatus);
		} catch (Throwable $th) {
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
