<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        if (Auth::guard('api')->check()) {
            $friends = Auth::guard('api')->user()->getFriends();
            $second_friends = Auth::guard('api')->user()->get2ndFriends();
            $unFriends = Auth::guard('api')->user()->getUnfriends();

            $posts = PostResource::collection(Post::with('likes', 'comments')->orderBy('created_at', 'DESC')->get());

            return response()->json(['posts' => $posts, 'users' => ['friends' => $friends, 'friends_2rd' => $second_friends, 'unfriends' => $unFriends]], 200);
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Post $post)
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            //$slug = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->title)));
            $input = $request->validate([
                'title' => 'required',
                'slug' => 'unique:posts|regex:/^[a-z0-9-]/'
            ]);
            $imageName = $request->file('image')->getClientOriginalName();
            $imageName = str_replace(' ', '-', $imageName);
            $data = $request->file('image')->storeAs('public/images', $imageName);
            $url = Storage::url($data);
            $image = env('APP_URL') . $url;
            $data = array(
                'title' => $input['title'],
                'user_id' => Auth::guard('api')->id(),
                'slug' => $input['slug'],
                'content' => $request['content'],
                'image' => $image,
                'type' => $request['type'],
                'status' => $request['status']
            );
            //$new_post = Post::create($data);

            if (Post::create($data)) {
                $new_post = PostResource::collection(Post::orderBy('created_at', 'DESC')->get());

                return response()->json(['status' => 'success', 'post' => $new_post, 'message' => 'Post created successfully.'], 200);
            }
            return response()->json(['status' => 'failed', 'message' => 'Can\'t create your post'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post)
    {
        if (Auth::guard('api')->check()) {
            $id = $request->id;
            // $post = Post::select('*')->where('id', $id)->get();
            $item = $post->find($id);
            return response()->json($item, 200);
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if (Auth::guard('api')->check()) {
            $item = $post->find($request->id);
            $input = $request->all();
            $item->fill($input)->save();
            return response()->json(['status' => 'success', 'message' => 'Post has been updated successfully.'], 200);
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Post $post)
    {
        if (Auth::guard('api')->check()) {
            $user_id = Auth::guard('api')->id();
            $item = $post->find($request->id);
            if ($item->user_id == $user_id) {
                $item->delete();
                return response()->json(['status' => 'success', 'message' => 'Post has been deleted successfully.'], 200);
            }
            return response()->json(['status' => 'failed', 'message' => 'You are not allowed to delete this post'], 200);
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }
    public function upload(Request $request)
    {
        $imageName = $request->file('image')->getClientOriginalName();
        $imageName = str_replace(' ', '-', $imageName);
        $data = $request->file('image')->storeAs('public/images', $imageName);
        $url = Storage::url($data);
        $url = env('APP_URL') . $url;
        return response()->json(['status' => 'success', 'url' => $url], 200);
    }
    public function like(Request $request, Post $post)
    {
        if (Auth::guard('api')->check()) {
            $user_id = Auth::guard('api')->id();
            $post_item = $post->find($request->post_id);
            $input = $request->validate([
                'post_id' => 'required'
            ]);
            $data = array(
                'user_id' => $user_id,
                'post_id' => $input['post_id']
            );
            $checkPost = $post->find($input['post_id']);
            if ($checkPost) {
                $checkLike = Like::where('post_id', $input['post_id'])->where('user_id', $user_id)->first();
                if ($checkLike) {
                    $checkLike->delete();
                    $likeCount = Like::select('*')->where('post_id', $input['post_id'])->get()->count();
                    return response()->json(['status' => 'success', 'counts' => $likeCount, 'message' => 'unliked'], 200);
                } else {
                    $like = Like::create($data);
                    if ($like) {
                        $likeCount = Like::select('*')->where('post_id', $input['post_id'])->get()->count();
                        return response()->json(['status' => 'success', 'counts' => $likeCount, 'message' => 'liked'], 200);
                    }
                }
            } else {
                return response()->json(['status' => 'failed', 'counts' => 0, 'message' => 'We can\'t find this post'], 400);
            }
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }
}
