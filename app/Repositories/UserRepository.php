<?php namespace LoveSick\Repositories;

use Facebook\Entities\AccessToken;
use LoveSick\User;
use Laravel\Socialite\Two\User as SocialiteUser;

/**
 * Class UserRepository
 * @package LoveSick\Repositories
 */
class UserRepository {

    /**
     * @param $socialiteUser
     * @return User
     */
    public function findByEmailOrCreate(SocialiteUser $socialiteUser)
    {
        $user = User::firstOrCreate([
            'email' => $socialiteUser->email
        ]);

        $user->addFacebookAccessToken(new AccessToken($socialiteUser->token));
        $user->save();

        return $user;
    }

}
