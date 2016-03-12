<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Validator;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;


	/**
	 * Create a new authentication controller instance.
	 */
	public function __construct()
	{
		$this->middleware('guest', ['except' => ['getLogout', 'getConfirmation', 'getConfirmationMail']]);
	}

	/**
	 * @param $user
	 */
	public function sendConfirmationMail($user)
	{
		$subject = 'Bestätige deine Registrierung bei HQ-Mirror.de';

		\Mail::send('emails.confirmation', compact('subject', 'user'), function ($message) use ($user, $subject) {
			$message->to($user->email)
				->subject($subject);
		});
	}

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

	public function getLogin()
	{
		$_previous = session()->has('_previous') ? session('_previous') : ['url' => '/'];
		session(['redirectPath' => $_previous['url']]);

		return view('auth.login');
	}


	public function redirectPath()
	{
		return session('redirectPath') ?  session('redirectPath') : '/';
	}


	public function getFailedLoginMessage()
	{
		return 'Login fehlgeschlagen';
	}


	public function getConfirmation($confirmation_code = null, Guard $auth)
	{
		$user = User::where('confirmation_code','=',$confirmation_code)->first();
		if($user) {
			$user->confirmed = 1;
			$user->confirmation_code = '';
			$user->save();
			$user->extendPremium(3, 'days');
			flash()->success('Vielen Dank! Deine Registrierung ist vollständig.');
		} else {
			flash()->error('Der Bestätigungs-Link ist ungültig.');
		}

		if($auth->guest()) {
			return redirect('/auth/login');
		} else {
			return redirect('/');
		}

	}


	public function getConfirmationMail($email, Registrar $registrar, Guard $auth)
	{
		$user = User::where('email','=',$email)->first();

		if(!$user) {
			flash()->error('Der Email-Adresse nicht gefunden.');
			return redirect('auth/login');
		}

		if($user->confirmed) {
			flash('Registrierung wurde bereits bestätigt');
			return redirect('/');
		}

		if(empty($user->confirmation_code)) {
			$user->confirmation_code = str_random(30);
			$user->save();
		}

		$registrar->sendConfirmationMail($user);

		flash('Bestätigungs-Mail wurde geschickt.');

		if($auth->guest()) {
			return redirect('auth/login');
		} else {
			return redirect('/');
		}

	}

}
