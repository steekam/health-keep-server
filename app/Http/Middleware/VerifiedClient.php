<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Throwable;

class VerifiedClient
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		try {
			throw_if(empty($request->client), new Exception('no client provided'));
			try {
				$client = Client::findOrFail($request->client);
			} catch (ModelNotFoundException $exception) {
				throw new Exception('No client record exists');
			}
			throw_unless($client->hasVerifiedEmail(), new Exception('Your email address in not verified'));
			throw_unless($request->expectsJson(), new Exception('Invalid request route'));
		} catch (Throwable $throwable) {
			abort(403, $throwable->getMessage());
		}
		return $next($request);
	}
}
