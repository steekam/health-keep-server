<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
	function home()
	{
			return view('client.home');
	}
}
