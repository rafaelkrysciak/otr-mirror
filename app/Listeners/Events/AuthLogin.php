<?php namespace App\Listeners\Events;

use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Guard;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class AuthLogin {

	/**
	 * @var Guard
	 */
	private $auth;


	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		//
		$this->auth = $auth;
	}


	/**
	 * Handle the event.
	 *
	 * @param User $user
	 */
	public function handle(User $user)
	{
		if(!$user->confirmed) {
			flash('Bitte bestätige die Registrierung. Bestätigung-Email <strong><a href="'.\URL::to('auth/confirmation-mail', [$user->email]).'">erneut senden</a></strong>');
			if($user->created_at->diffInDays(Carbon::now()) > 7) {
				$this->auth->logout();
			}
		}
	}

}
