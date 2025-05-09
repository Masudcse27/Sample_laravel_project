<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     @OA\Property(property="id", type="integer", example=3),
 *     @OA\Property(property="title", type="string", example="masud"),
 *     @OA\Property(property="content", type="string", example="masudcse@gmail.com")
 * )
 */
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
            'id'=>$this->id,
            'title'=>$this->title,
            'content'=>$this->content,
            'user'=>new UserResource($this->whenLoaded('user'))
        ];
    }
}
