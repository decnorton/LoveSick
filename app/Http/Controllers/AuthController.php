<?php namespace LoveSick\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use LoveSick\Http\Auth\AuthenticateUser;
use LoveSick\Http\Auth\AuthenticateUserListener;
use LoveSick\Http\Requests\LoginRequest;
use LoveSick\Http\Requests\RegisterRequest;
use LoveSick\User;

/**
 * Class AuthController
 * @package LoveSick\Http\Controllers
 */
class AuthController extends Controller implements AuthenticateUserListener {

	/**
	 * Show the application login form.
	 *
	 * @param AuthenticateUser $auth
	 * @param Request $request
	 * @return Response
	 */
	public function getLogin(AuthenticateUser $auth, Request $request)
	{
		return $auth->execute($request->has('code'), $this);
	}


	/**
	 * @param User $user
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
	 */
	public function onLoggedIn(User $user)
	{
		return redirect('/');
	}

	/**
	 * @param Guard $auth
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
	public function getLogout(Guard $auth)
	{
		$auth->user()->removeFacebookAccessTokens();
		$auth->logout();

		return redirect('/');
	}

	/**
	 * @return mixed
	 */
	public function onLoginFailed()
	{
		return redirect('')->withErrors([
			'facebook' => 'Facebook login failed'
		]);
	}
}
