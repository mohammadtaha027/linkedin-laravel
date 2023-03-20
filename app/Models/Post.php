<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Comment;


class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'type',
        'image',
        'status'
    ];
    public function getPosts($post)
    {
        $posts = $post->all()->map(function($item){
            $comments = Comment::getCommentsByPostId($item -> id);
            $commentsCounts = count(Comment::where('post_id', $item -> id)->get()->toArray());
            usort($comments, fn($a, $b) => $a['level'] <=> $b['level']);
            return array(
                'id' => $item -> id,
                'title'=>$item->title,
                'content'=>$item->content,
                'image'=> $item->image,
                'type'=> $item->type,
                'status'=> $item->status,
                'frinds_only'=> $item->friends_only,
                'created_at'=> $item->created_at,
                'updated_at'=>$item->updated_at,
                'likes' => Like::select('*')->where('post_id', $item -> id)->get()->count(),
                'checklike' => like::where('post_id', $item -> id)->where('user_id', Auth::guard('api')->id())->first(),
                'comment_count' => $commentsCounts,
                'comments' => $comments
            );
        })->sortByDesc('created_at');
        $posts  = array_values($posts->toArray());
        return $posts;
    }
}
