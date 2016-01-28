<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Input;
use \Mail;
use Illuminate\Http\Request;


class ContactController extends Controller {

	public function form()
	{
		return view('contact_form');
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
