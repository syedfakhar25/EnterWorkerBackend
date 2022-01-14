<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'email',
        'gender',
        'designation_id',
        'designation',
        'img',
        'user_type',
        'manager_type',
        'company',
        'address',
        'project_location',
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function messages(){

        return $this->hasMany(Chat::class);

    }

    public function project(){
        return $this->hasMany(Project::class,'manager_id');
    }

    public function designation(){
        return $this->hasOne(Designation::class);
    }


    //employees of project team (members to assign in a project team)
    public function projectTeam(){
        return $this->belongsTo(ProjectTeam::class,'employee_id');
    }
    //employees of project team (members to assign in a project team)
    public function companyTeam(){
        return $this->belongsTo(CompanyTeam::class,'employee_id');
    }
}
