<?php

namespace DavidSchneider\LaravelCrewmate;

use DavidSchneider\LaravelCrewmate\Database\Factories\CrewmateFactory;
use DavidSchneider\LaravelCrewmate\Notifications\PasswordResetRequestNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Crewmate extends Authenticatable
{
	use Notifiable, HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new PasswordResetRequestNotification($token));
	}

	/**
	 * Get factory for this model
	 *
	 * @return mixed
	 */
	protected static function newFactory()
	{
		return CrewmateFactory::new();
	}
}
