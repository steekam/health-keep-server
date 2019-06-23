<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Reminder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AppointmentController extends Controller
{
	public $errorStatus = 422;

	public function __construct()
	{
		$this->middleware('verified_client')->only(['store', 'index']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param $client
	 * @return void
	 */
	public function index($client)
	{
		$client = Client::findOrfail($client);
		return response()->json($client->appointments);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @param $client_id
	 * @return Response
	 */
	public function store(Request $request, $client_id)
	{
		$data = $request->all();
		Validator::make($data, [
			'appointment' => 'required|array',
			'reminder' => 'required|array'
		])->validate();
		Validator::make($data['appointment'], [
			'title' => 'required',
			'appointment_date' => 'required|date_format:Y-m-d',
			'appointment_time' => 'required|date_format:H:i:s'
		])->validate();
		Validator::make($data['reminder'], [
			'start_date' => 'required|date_format:Y-m-d',
			'reminder_time' => 'required|date_format:H:i:s',
			'repeat' => 'nullable|digits_between:0,1',
			'frequency' => 'nullable|alpha|requiredIf:repeat,1'
		])->validate();
		try {
			throw_if(empty($client_id), new Exception('Client id is required'));
			$client = Client::findOrFail($client_id);
			$appointment = new Appointment($data['appointment']);
			$client->appointments()->save($appointment);
			$reminder = new Reminder($data['reminder']);
			$appointment->reminder()->save($reminder);
			$appointment->load('reminder');
			return response()->json($appointment);
		} catch (Throwable $th) {
			return response()->json(['error' => $th->getMessage()]);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param $appointment_id
	 * @return void
	 */
	public function show($appointment_id)
	{
		$appointment = Appointment::findOrFail($appointment_id);
		return response()->json($appointment);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param $appointment_id
	 * @return void
	 */
	public function update(Request $request, $appointment_id)
	{
		$data = $request->all();
		Validator::make($data, [
			'appointment' => 'nullable|array',
			'reminder' => 'nullable|array'
		])->validate();
		if(isset($data['appointment'])) {
			Validator::make($data['appointment'], [
				'title' => 'nullable|string',
				'appointment_date' => 'nullable|date_format:Y-m-d',
				'appointment_time' => 'nullable|date_format:H:i:s',
				'archived' => 'nullable|digits_between:0,1'
			])->validate();
		}

		if(isset($data['reminder'])) {
			Validator::make($data['reminder'], [
				'start_date' => 'nullable|date_format:Y-m-d',
				'reminder_time' => 'nullable|date_format:H:i:s',
				'repeat' => 'nullable|digits_between:0,1',
				'frequency' => 'nullable|alpha|requiredIf:repeat,1',
				'active' => 'nullable|digits_between:0,1'
			])->validate();
		}
		try {
			if (empty($appointment_id)) throw new Exception('Appointment id is required');
			if (isset($data['appointment'])) {
				Appointment::where('appointment_id', $appointment_id)
					->update($data['appointment']);
			}
			if (isset($data['reminder'])) {
				$appointment = Appointment::findOrFail($appointment_id);
				Reminder::where('reminder_id', $appointment->reminder->reminder_id)
					->update($data['reminder']);
			}
			$appointment = Appointment::findOrFail($appointment_id);
			return response()->json($appointment);
		} catch (Throwable $th) {
			return response()->json(['error' => $th->getMessage()], $this->errorStatus);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $appointment_id
	 * @return Response
	 */
	public function destroy($appointment_id)
	{
		try {
			throw_if(empty($appointment_id), new Exception('Appointment_id is required'));
			Appointment::destroy($appointment_id);
			return response()->json(['success' => 'Appointment deleted completely']);
		} catch (Throwable $th) {
			return response()->json(['error' => $th->getMessage()], $this->errorStatus);
		}
	}
}
