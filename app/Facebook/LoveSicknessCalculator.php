<?php namespace LoveSick\Facebook;

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use LoveSick\User;

class LoveSicknessCalculator {


    /**
     * @var User
     */
    private $user;
    private $session;

    function __construct(User $user)
    {
        $this->user = $user;
        $facebookAccessToken = $user->getFacebookAccessToken();

        $this->session = new FacebookSession($facebookAccessToken->getAccessToken());
    }

    public function calculate()
    {
        return $this->getFriendsList();
    }

    public function getFriendsList()
    {
        $list = $this->makeGraphRequest('/me/friends');
        dd($list);
    }

    private function makeGraphRequest($path, $graphObjectType = 'Facebook\GraphObject')
    {
        try {
            $request = new FacebookRequest(
                $this->session, 'GET', $path
            );

            return $request->execute()->getGraphObject($graphObjectType);
        } catch (FacebookRequestException $e) {
            // The Graph API returned an error
        } catch (\Exception $e) {
            // Some other error occurred
        }
    }
}
