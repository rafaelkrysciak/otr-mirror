<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

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
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => ['getLogout', 'getConfirmation', 'getConfirmationMail']]);
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


	public function getConfirmation($confirmation_code = null)
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

		if($this->auth->guest()) {
			return redirect('/auth/login');
		} else {
			return redirect('/');
		}

	}


	public function getConfirmationMail($email, Registrar $registrar)
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

		if($this->auth->guest()) {
			return redirect('auth/login');
		} else {
			return redirect('/');
		}

	}

}
