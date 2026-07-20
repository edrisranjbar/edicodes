<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CommentResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'content' => $this->content,
            'image' => $this->image,
            'published' => $this->published,
            'published_at' => $this->published_at,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'comments' => CommentResource::collection($this->whenLoaded('approvedComments')),
            'comments_count' => $this->whenCounted('approvedComments'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
