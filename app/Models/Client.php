<?php

namespace App\Models;

use App\Mail\ForgotPasswordEmail;
use App\Notifications\ClientResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
	use  Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'username', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password'
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	protected $primaryKey = 'client_id';

	/**
	 * Roles relationships
	 */
	function roles() {
		return $this->belongsToMany('App\Models\Client_role','client_map_roles','client_id','client_role_id');
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ClientResetPasswordNotification($token));
	}
}
