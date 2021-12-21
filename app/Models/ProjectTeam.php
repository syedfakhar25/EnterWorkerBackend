<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTeam extends Model
{
    use HasFactory;
    protected $guarded = ['*'];
    public function employees(){

        return $this->hasMany(User::class, 'employee_id');
    }

    public function project(){
        return $this->hasOne(Project::class,'project_id');
    }
}
