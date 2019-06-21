<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientVerificationController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Email Verification Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling email verification for any
	| user that recently registered with the application. Emails may also
	| be re-sent if the user didn't receive the original email message.
	|
	*/

	use VerifiesEmails;

	/**
	 * Where to redirect users after verification.
	 *
	 * @var string
	 */
	protected $redirectTo = '/client/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('signed')->only('verify');
		$this->middleware('throttle:6,1')->only('verify', 'resend');
	}

	//? Email verification

	/**
	 * Mark the authenticated user's email address as verified.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function verify(Request $request)
	{
		$client = Client::findOrFail($request['id']);
		if ($client->hasVerifiedEmail()) {
			return redirect($this->redirectPath())->with('status', 'Your email is already verified');
		}

		if ($client->markEmailAsVerified()) {
			event(new Verified($client));
		}

		return redirect($this->redirectPath())->with('status', "You have successfully verified your email. You can now login");
	}

	/**
	 * Resend the email verification notification.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function resend(Request $request)
	{
		$client = Client::findOrFail($request['id']);
		if ($client->hasVerifiedEmail()) {
			return response()->json(['response'=>'Your email is already verified']);
		}
		$client->sendEmailVerificationNotification();
		return response()->json(['response' => 'A resent link has been sent to your email']);
	}


}
