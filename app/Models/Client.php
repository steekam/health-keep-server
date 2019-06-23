<?php

namespace App\Models;

use App\Notifications\ClientResetPasswordNotification;
use App\Notifications\ClientVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable implements MustVerifyEmail
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
	function roles()
	{
		return $this->belongsToMany('App\Models\Client_role', 'client_map_roles', 'client_id', 'client_role_id');
	}

	/**
	 * Appointment relationship
	 * @return HasMany
	 */
	function appointments()
	{
		return $this->hasMany('App\Models\Appointment','client_id', 'client_id');
	}

	/**
	 * Get appointment reminders for clients
	 * @return HasManyThrough
	 */
	function appointment_reminders() {
		return $this->hasManyThrough('App\Models\Reminder', 'App\Models\Appointment');
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param string $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new ClientResetPasswordNotification($token));
	}

	/**
	 * Send the email verification notification.
	 *
	 * @return void
	 */
	public function sendEmailVerificationNotification()
	{
		$this->notify(new ClientVerifyEmailNotification());
	}


}
