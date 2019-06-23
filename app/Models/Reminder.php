<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
	protected $primaryKey = 'reminder_id';
	protected $guarded = [];
	public $timestamps = false;

	function reminderble()
	{
		return $this->morphTo();
	}
}
