<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client_role extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'role_name'
	];

	protected $primaryKey = 'client_role_id';
	/**
	 * Get clients with the role
	 */
	function clients() {
		return $this->belongsToMany('App\Models\Client','client_map_roles','client_role_id','client_id');
	}
}
