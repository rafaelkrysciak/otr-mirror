<?php namespace App\Services;

use App\User;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use \Mail;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
		$confirmation_code = str_random(30);

		$user = User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'confirmation_code' => $confirmation_code,
		]);

		$this->sendConfirmationMail($user);

		return $user;
	}


	/**
	 * @param $user
	 */
	public function sendConfirmationMail($user)
	{
		$subject = 'Bestätige deine Registrierung bei HQ-Mirror.de';

		Mail::send('emails.confirmation', compact('subject', 'user'), function ($message) use ($user, $subject) {
			$message->to($user->email)
				->subject($subject);
		});
	}

}
