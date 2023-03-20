<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function create(Request $request, Comment $comment)
    {
        if(Auth::guard('api')->check()){
            $input = $request->validate([
                'comment' => 'required|string',
                'post_id' => 'required',
                'parent_id' => 'required'
            ]);
            if($input['parent_id'] == 0 || $input['parent_id'] == null){
                $level = 0;
            }else{
                $parent_level = Comment::select('level')->where('id', $input['parent_id'])->first();
                $level = $parent_level['level'] + 1;
            }

            $data = array(
                'post_id' => $input['post_id'],
                'user_id' => Auth::guard('api')->id(),
                'comment' => $input['comment'],
                'parent_id' => $input['parent_id'],
                'level' => $level
            );
            //return response()->json(['status'=>'success', 'level'=>$data], 200);
            $comment = Comment::create($data);
            if($comment){
                $comments = Comment::getCommentsByPostId($input['post_id']);
                $commentsCounts = count(Comment::where('post_id', $input['post_id'])->get()->toArray());
                //usort($comments, fn($a, $b) => $a['level'] <=> $b['level']);
                return response()->json(['status'=>'success', 'comments'=>$comments, 'comment_count' => $commentsCounts, 'message'=> 'Commented Successfully'], 200);
            }else{
                return response()->json(['status'=>'failed', 'message'=>'Can\'t comment'], 200);
            }
        }
        return response()->json(['status' => 'failed', 'message' => 'Unauthenticated'], 200);
    }

    public function getComments(Request $request)
    {
        $comments = Comment::getCommentsByPostId($request->post_id);
        $commentsCounts = count(Comment::where('post_id', $request->post_id)->get()->sortByDesc('created_at')->toArray());
        return response()->json(['comments'=>$comments, 'count'=>$commentsCounts], 200);
    }

    /**
     * Display a listing of the resource.
     */

}
