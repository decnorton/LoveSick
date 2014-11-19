<?php namespace LoveSick\Http\Auth;

use LoveSick\User;

/**
 * Interface AuthenticateUserListener
 * @package LoveSick\Http\Auth
 */
interface AuthenticateUserListener {

    /**
     * @param User $user
     * @return mixed
     */
    public function onLoggedIn(User $user);

    /**
     * @return mixed
     */
    public function onLoginFailed();

}
