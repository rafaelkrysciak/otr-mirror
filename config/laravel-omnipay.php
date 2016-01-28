<?php

return [

	// The default gateway to use
	'default' => 'paypal',

	// Add in each gateway here
	'gateways' => [
		'paypal' => [
			'driver'  => 'PayPal_Express',
			'options' => [
				'solutionType'   => 'Sole',
				'landingPage'    => 'Login',
				'username'       => env('PAYPAL_USERNAME'),
				'password'       => env('PAYPAL_PASSWORD'),
				'signature'      => env('PAYPAL_SIGNATURE'),
				'logoImageUrl'   => 'https://dl.dropboxusercontent.com/u/1279118/hq-mirror/hq_mirror_paypal_logo.png',
				'headerImageUrl' => 'https://dl.dropboxusercontent.com/u/1279118/hq-mirror/hq_mirror_paypal_logo_big.png',
				'brandName'      => 'HQ-Mirror',
				'testMode'       => env('PAYPAL_TESTMODE'),
			]
		]
	]

];