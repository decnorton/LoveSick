<?php namespace LoveSick\Http\Auth;

use Facebook\Entities\AccessToken;
use Facebook\FacebookSession;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Contracts\Factory as Socialite;
use LoveSick\Repositories\UserRepository;
use LoveSick\User;

/**
 * Class AuthenticateUser
 * @package LoveSick\Http\Auth
 */
class AuthenticateUser
{

    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var Factory
     */
    private $socialite;

    /**
     * @var Authenticatable
     */
    private $auth;

    /**
     * @var array
     */
    protected $scopes = [
        'email',
        'public_profile',
        'user_friends'
    ];

    /**
     * @param UserRepository $users
     * @param Socialite $socialite
     * @param Guard $auth
     */
    function __construct(UserRepository $users, Socialite $socialite, Guard $auth)
    {
        $this->users = $users;
        $this->socialite = $socialite;
        $this->auth = $auth;
    }

    /**
     * @param $hasCode
     * @param AuthenticateUserListener $listener
     * @return mixed
     */
    public function execute($hasCode, AuthenticateUserListener $listener)
    {
        if (!$hasCode) {
            return $this->getAuthorizationFirst();
        }

        $user = $this->users->findByEmailOrCreate($this->getFacebookUser());

        if ($user) {
            $this->extendFacebookAccessToken($user);

            $this->auth->login($user, true);

            return $listener->onLoggedIn($user);
        }

        return $listener->onLoginFailed();
    }

    public function extendFacebookAccessToken(User $user)
    {
        $accessToken = $user->getFacebookAccessToken();

        if ($accessToken) {
            $token = new AccessToken($accessToken->access_token);
            $newToken = $token->extend();

            $user->removeFacebookAccessToken($token);
            $user->addFacebookAccessToken($newToken);
            $user->save();
        }

        return $user;
    }

    /**
     * @return mixed
     */
    public function getAuthorizationFirst()
    {
        return $this->socialite->driver('facebook')
            ->scopes($this->scopes)
            ->redirect();
    }

    /**
     * @return mixed
     */
    private function getFacebookUser()
    {
        return $this->socialite->driver('facebook')
            ->scopes($this->scopes)
            ->user();
    }
}
