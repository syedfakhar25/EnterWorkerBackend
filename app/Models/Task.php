<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use App\Models\User;
use App\Models\Project;

class Task extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function employee(){
    	return $this->belongsTo(User::class,'employee_id');
    }
    public function project(){
    	return $this->belongsTo(Project::class,'project_id');
    }
}
