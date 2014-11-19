<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'stripe' => [
		'model'  => 'User',
		'secret' => '',
	],

	'facebook' => [
		'client_id' => getenv('FACEBOOK_APP_ID'),
		'client_secret' => getenv('FACEBOOK_APP_SECRET'),
		'redirect' => 'http://lovesick.app:8000/login'
	]

];
