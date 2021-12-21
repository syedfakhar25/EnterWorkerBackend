<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Project;
use App\Models\Calenderevent;

class Task extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function employee(){
    	return $this->belongsTo(User::class,'employee_id')->withDefault();
    }
    public function step(){
    	return $this->belongsTo(Step::class,'step_id');
    }
    public function project(){
        return $this->belongsTo(Step::class,'project_id');
    }
    public function event(){
    	return $this->hasOne(Calenderevent::class,'task_id')->withDefault();
    }
}
