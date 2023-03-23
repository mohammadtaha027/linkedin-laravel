<?php

namespace App\Http\Resources;

use App\Helpers\CommentHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $comments = CommentHelper::getCommentsByPostId($this->id);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'type' => $this->type,
            'status' => $this->status,
            'frinds_only' => $this->friends_only,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'likes' => $this->likes->count(),
            'checklike' => $this->likes->where('user_id', auth()->guard('api')->id())->count() > 0,
            'comment_count' => $this->comments->count(),
            'comments' => $comments
        ];
    }
}
