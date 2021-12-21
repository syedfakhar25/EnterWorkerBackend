<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }
    public function tasks(){
        return $this->hasMany(Task::class,'step_id');
    }
}
