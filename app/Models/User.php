<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Friends;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFriends()
    {
        $friends = Friends::where('first_user', $this->id)->orWhere('second_user', $this->id)->get();
        $users = array_merge($friends->pluck('first_user')->toArray(), $friends->pluck('second_user')->toArray());

        return User::whereIn('id', $users)->where('id', '<>', $this->id)->get();
    }

    public function get2ndFriends()
    {
        $friends = $this->getFriends();
        $chk = [];
        foreach($friends as $u) $chk[$u->id] = true;
        $result = [];
        foreach($friends as $u) {
            $sec = $u->getFriends();
            foreach($sec as $u2) {
                if(empty($chk[$u2->id]) && $u2->id != $this->id) {
                    $chk[$u2->id] = true;
                    $result[] = $u2;
                }
            }
        }
        return $result;
    }

    public function getUnfriends()
    {
        $friends = Friends::where('first_user', $this->id)->orWhere('second_user', $this->id)->get();
        $users = array_merge($friends->pluck('first_user')->toArray(), $friends->pluck('second_user')->toArray());

        return User::whereNotIn('id', $users)->where('id', '<>', $this->id)->get();
    }
}
