<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    { 
        return [
       'id' => $this->id,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'email' => $this->email,
        'phone' => $this->phone,
        'designation' => $this->designation,
        'gender' => $this->gender,
        'img'=>asset('user_images/' . $this->img),
        'user_type' => $this->user_type,
        'created_at' => (string) $this->created_at,
        'updated_at' => (string) $this->updated_at,
    ];
    }
}
