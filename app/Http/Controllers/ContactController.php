<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Input;
use \Mail;
use Illuminate\Http\Request;
use ReCaptcha\ReCaptcha;


class ContactController extends Controller {

	public function form()
	{
		$values = ['name' => '', 'email' => ''];
		if(\Auth::user()) {
			$values = [
				'name' => \Auth::user()->name,
				'email' => \Auth::user()->email
			];
		}

		return view('contact_form', compact('values'));
	}

	public function send(Request $request)
	{
		$this->validate($request, [
			'email' => 'email',
			'message' => 'required|min:10',
		],[
			'required' => 'Bitte füge eine Nachricht ein.',
			'email' => 'Die Email-Adresse scheint ungültig zu sein.',
			'min' => 'Die Nachricht sollte länger als :min Zeichen lang sein.'
		]);

		if(!\Auth::user()) {
			$recaptcha = new ReCaptcha(config('hqm.recaptcha_secret'));
			$resp = $recaptcha->verify(Input::get('g-recaptcha-response'), $request->getClientIp());
			if (!$resp->isSuccess()) {
				return redirect()
					->back()
					->withInput()
					->withErrors(['Das Captcha war leider falsch. Bitte versuche noch mal']);
			}
		}


		$name = Input::get('name', 'unknown');
		$email = Input::get('email', 'unknown');
		$comment = Input::get('message', 'empty');

		Mail::send('emails.contact', compact('name', 'email', 'comment'), function($message) use ($email)
		{
			$message->to('info@hq-mirror.de', 'HQ-Mirror')
				->subject('User Message! #'.date('YmdHis'));
			if($email) {
				$message->replyTo($email);
			}
		});

		flash('Danke für deine Nachricht. Wir werden uns melden wenn erforderlich.');
		return redirect()->back();
	}
}
