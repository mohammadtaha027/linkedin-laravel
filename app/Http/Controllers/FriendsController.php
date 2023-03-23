<?php

namespace App\Http\Controllers;

use App\Models\Friends;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function Friend(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $first_user = Auth::guard('api')->id();
            $input = $request->validate([
                'second_user' => 'required'
            ]);
            $second_user = $input['second_user'];
            $data = array(
                'first_user' => $first_user,
                'second_user' => $second_user,
                'status' => 'pending'
            );
            $friend = Friends::where(function ($query) use ($first_user, $second_user) {
                $query->where('first_user', $first_user);
                $query->where('second_user', $second_user);
            })->orWhere(function ($query) use ($first_user, $second_user) {
                $query->where('second_user', $first_user);
                $query->where('first_user', $second_user);
            })->first();
            if ($friend) {
                return response()->json(['status' => 'failed', 'message' => 'You are Friend Already'], 200);
            } else {
                if (Friends::create($data)) {
                    $friends = Auth::guard('api')->user()->getFriends();
                    $second_friends = Auth::guard('api')->user()->get2ndFriends();
                    $unFriends = Auth::guard('api')->user()->getUnfriends();
                    return response()->json(['status' => 'success', 'users' => ['friends' => $friends, 'friends_2rd' => $second_friends, 'unfriends' => $unFriends]], 200);
                }
                return response()->json(['status' => 'failed', 'message' => 'Please try again later'], 401);
            }
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }
    public function unFriend(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $first_user = Auth::guard('api')->id();
            $input = $request->validate([
                'second_user' => 'required'
            ]);
            $second_user = $input['second_user'];
            $friend = Friends::where(function ($query) use ($first_user, $second_user) {
                $query->where('first_user', $first_user);
                $query->where('second_user', $second_user);
            })->orWhere(function ($query) use ($first_user, $second_user) {
                $query->where('second_user', $first_user);
                $query->where('first_user', $second_user);
            })->first();
            if ($friend) {
                if ($friend->delete()) {
                    $friends = Auth::guard('api')->user()->getFriends();
                    $second_friends = Auth::guard('api')->user()->get2ndFriends();
                    $unFriends = Auth::guard('api')->user()->getUnfriends();
                    return response()->json(['status' => 'success', 'users' => ['friends' => $friends, 'friends_2rd' => $second_friends, 'unfriends' => $unFriends]], 200);
                }
                return response()->json(['status' => 'failed', 'message' => 'Please try again later'], 401);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'You are not friend'], 200);
            }
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }
}
