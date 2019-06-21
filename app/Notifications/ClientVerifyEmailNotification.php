<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as DefaultVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class ClientVerifyEmailNotification extends DefaultVerifyEmail
{
	/**
	 * Get the verification URL for the given notifiable.
	 *
	 * @param mixed $notifiable
	 * @return string
	 */
	protected function verificationUrl($notifiable)
	{
		return URL::temporarySignedRoute(
			'client.verification.verify',
			Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
			['id' => $notifiable->getKey()]
		);
	}

}
