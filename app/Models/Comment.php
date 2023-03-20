<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'parent_id',
        'level'
    ];
    public static function getCommentsByPostId(int $post_id)
    {
        $comments = Comment::where('post_id', $post_id)->get()->sortByDesc('created_at');

        function makeComments($current, $comments) {
            $replies = [];
            foreach($comments as $x) {
                if($x->parent_id === $current['id']) {
                    $replies[] = makeComments($x, $comments);
                }
            }
            $res = $current->toArray();
            $res['replies'] = $replies;

            return $res;
        };

        $result = [];
        foreach($comments as $x) {
            if($x->parent_id === 0) $result[] = makeComments($x, $comments);
        }
        return $result;
    }
}
