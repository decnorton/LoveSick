<?php namespace LoveSick\Auth;

use Carbon\Carbon;
use Facebook\Entities\AccessToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class FacebookAccessToken
 * @package LoveSick\Auth
 */
class FacebookAccessToken extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'facebook_access_tokens';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'access_token',
        'expires'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expires'];

    /**
     * FacebookAccessToken belongs to a User.
     *
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }

    /**
     * Creates an AccessToken from stored data.
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        return new AccessToken(
            $this->access_token,
            $this->expires ? $this->expires->getTimestamp() : null
        );
    }
}
