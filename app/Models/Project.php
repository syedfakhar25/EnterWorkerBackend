<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Task;
use App\Models\Pinproject;

class Project extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function customer(){
    	return $this->belongsTo(User::class,'customer_id');
    }
    public function manager(){
        return $this->belongsTo(User::class,'manager_id');
    }
    public function company_worker(){
        return $this->belongsTo(User::class,'company_worker_id');
    }
    public function steps(){
    	return $this->hasMany(Step::class,'project_id');
    }
    public function tasks(){
        return $this->hasMany(Task::class,'project_id');
    }
    public function pinnedproject(){
    	return $this->hasMany(Pinproject::class,'project_id');
    }

    //team of project (members to assign in a project)
    public function projectTeam(){
        return $this->belongsTo(ProjectTeam::class,'project_id');
    }

    public function companyTeam(){
        return $this->belongsTo(CompanyTeam::class,'project_id');
    }

    //project pictures
    public function projectPictures(){
        return $this->hasMany(ProjectPicture::class,'project_id');
    }

    //project extra work for employee
    public function extraWork(){
        return $this->hasMany(ExtraWork::class,'project_id');
    }

}
