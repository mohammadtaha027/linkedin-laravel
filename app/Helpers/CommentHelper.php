<?php

namespace App\Helpers;
use App\Models\Comment;

function makeComments($current, $comments)
{
    $replies = [];
    foreach($comments as $x) {
        if($x->parent_id === $current['id']) {
            $replies[] = makeComments($x, $comments);
        }
    }
    $res = $current->toArray();
    $res['replies'] = $replies;

    return $res;
}

class CommentHelper {

    public static function getCommentsByPostId(int $post_id)
    {
        $comments = Comment::where('post_id', $post_id)->get()->sortByDesc('created_at');

        $result = [];
        foreach($comments as $x) {
            if($x->parent_id === 0) $result[] = makeComments($x, $comments);
        }
        return $result;
    }
}
