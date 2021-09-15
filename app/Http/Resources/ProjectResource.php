<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
        'name' => $this->name,
        'description' => $this->description,
        'street' => $this->street,
        'postal_code' => $this->postal_code,
        'city' => $this->city,
        'start_date' => $this->start_date,
        'end_date' => $this->end_date,
        'created_at' => (string) $this->created_at,
        'updated_at' => (string) $this->updated_at,
        'customer' => $this->customer,
        ];
    }
}
