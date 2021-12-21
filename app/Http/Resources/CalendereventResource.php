<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CalendereventResource extends JsonResource
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
        'title' => $this->title,
        'color' => json_decode($this->color),
        'allDay' => $this->allDay,
        'draggable' => $this->draggable,
        'resizable' => json_decode($this->resizable),
        'start' =>(string) $this->start,
        'end' => (string) $this->end,
        'created_at' => (string) $this->created_at,
        'updated_at' => (string) $this->updated_at,
        ];
    }
}
