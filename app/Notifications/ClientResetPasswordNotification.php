<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ClientResetPasswordNotification extends Notification
{
	use Queueable;

	public $token;

	/**
	 * Create a new notification instance.
	 *
	 * @param $token
	 */
	public function __construct($token)
	{
		$this->token = $token;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		$reset_link = url(config('app.url').route('client.password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));
		return (new MailMessage)
			->subject('Reset Password Notification')
			->greeting('Hello!')
			->line('You are receiving this email because we received a password reset request for your account.')
			->action('Reset Password', $reset_link)
			->line('This password reset link will expire in '.config('auth.passwords.clients.expire').' minutes.')
			->line('If you did not request for a password reset, no further action is required');
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
