<?php namespace LoveSick;

use Carbon\Carbon;
use Facebook\Entities\AccessToken;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LoveSick\Auth\FacebookAccessToken;

/**
 * Class User
 * @package LoveSick
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

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
        'email',
        'name'
    ];

    /**
     * A user can have many FacebookAccessTokens
     *
     * @return HasMany
     */
    public function tokens()
    {
        return $this->hasMany('LoveSick\Auth\FacebookAccessToken', 'user_id', 'id');
    }

    /**
     * Find a FacebookAccessToken with the same access_token and remove it if it exists.
     *
     * @param AccessToken $token
     */
    public function removeFacebookAccessToken(AccessToken $token)
    {
        $token = $this->tokens()->where('access_token', (string)$token)->first();

        if ($token != null) {
            $token->delete();
        }
    }

    /**
     * Remove all attached FacebookAccessTokens.
     */
    public function removeFacebookAccessTokens()
    {
        foreach ($this->tokens()->getResults() as $token) {
            $token->delete();
        }
    }

    /**
     * Create a FacebookAccessToken from an AccessToken.
     *
     * @param AccessToken $token
     * @return Model
     */
    public function addFacebookAccessToken(AccessToken $token)
    {
        return $this->tokens()->create([
            'access_token' => (string)$token,
            'expires' => $token->getExpiresAt() ? Carbon::instance($token->getExpiresAt()) : Carbon::now()->addHour()
        ]);
    }

    /**
     * Get's the FacebookAccessToken with the longest expiry.
     *
     * @return FacebookAccessToken
     */
    public function getFacebookAccessToken()
    {
        return $this->tokens()->orderBy('expires', 'desc')->first();
    }

}
