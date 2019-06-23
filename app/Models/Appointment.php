<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Appointment extends Model
{
	protected $primaryKey = 'appointment_id';
	protected $guarded = [];
	protected $with = ['reminder'];

	/**
	 * Client relationship
	 * @return BelongsTo
	 */
	function client()
	{
		return $this->belongsTo('App\Models\Client', 'client_id', 'client_id');
	}

	/**
	 * @return MorphOne
	 */
	function reminder()
	{
		return $this->morphOne('App\Models\Reminder','reminderble');
	}
}
