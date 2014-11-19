<?php namespace LoveSick\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use LoveSick\Facebook\LoveSicknessCalculator;
use LoveSick\User;

/**
 * Class LoveSicknessController
 * @package LoveSick\Http\Controllers
 */
class LoveSicknessController extends Controller
{

    /**
     * @var Guard
     */
    private $auth;


    /**
     * @var User
     */
    private $user;

    /**
     * @param Guard $auth
     */
    function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->user = $auth->user();

        $this->calculator = new LoveSicknessCalculator($this->user);
    }

    /**
     * @return string
     */
    public function calculate()
    {
        return $this->calculator->calculate();
        return 'calculating';
    }

}
