<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskRescource extends JsonResource
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
        'percentage' => $this->percentage,
        'deadline' => (string) $this->deadline,
        'created_at' => (string) $this->created_at,
        'updated_at' => (string) $this->updated_at,

        'employee' => [
            'id' => $this->employee->id,
            'first_name' => $this->employee->first_name,
            'last_name' => $this->employee->last_name,
            'email' => $this->employee->email,
            'phone' => $this->employee->phone,
            'designation' => $this->employee->designation,
            'gender' => $this->employee->gender,
            'img'=>asset('user_images/' . $this->employee->img),
            'user_type' => $this->employee->user_type,
            'created_at' => (string) $this->employee->created_at,
            'updated_at' => (string) $this->employee->updated_at,
           ],

        'project' => $this->project,
        'project_customer' =>[
            'id' => $this->project->customer->id,
            'first_name' => $this->project->customer->first_name,
            'last_name' => $this->project->customer->last_name,
            'email' => $this->project->customer->email,
            'phone' => $this->project->customer->phone,
            'designation' => $this->project->customer->designation,
            'gender' => $this->project->customer->gender,
            'img'=>asset('user_images/' . $this->project->customer->img),
            'user_type' => $this->project->customer->user_type,
            'created_at' => (string) $this->project->customer->created_at,
            'updated_at' => (string) $this->project->customer->updated_at,
           ],
        ];
    }
}
