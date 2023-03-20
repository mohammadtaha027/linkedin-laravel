<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Friends;

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
    public static function getUsers($myId)
    {
        $users = User::all();
        $friends_id = Friends::where('status', 'active')->where('user1_id', $myId)->orWhere('user2_id', $myId)->get();
        $friends = [];
        $friends_2rd = [];
        foreach($friends_id as $id){
            if($id->user1_id == $myId){
                $friend = User::findOrFail($id->user2_id);
            }elseif($id->user2_id == $myId){
                $friend = User::findOrFail($id->user1_id);
            }
            // $key = array_search($friend, $users);
            // unset($users[$key]);
            array_push($friends, $friend);
        }
        // foreach($friends_id as $id){
        //     $item = User::where('id', $id)->get()->toArray();
        //     $key = array_search($item, $users);
        //     unset($users[$key]);
        //     array_push($friends, $item[0]);
        //     $friends_2rd_id = Friends::where('status', 'active')
        //                     ->where('user1_id', $item[0]['id'])
        //                     ->orWhere('user2_id', $item[0]['id'])
        //                     ->get()->toArray();
        //     foreach($friends_2rd_id as $friend_id){
        //         $friend = User::where('id', $friend_id)->get()->toArray();
        //         array_push($friends_2rd, $friend[0]);
        //     }
        // }

        return ['users'=>$users, 'friends'=>$friends, 'friends_2rd'=> $friends_2rd];
    }
}
