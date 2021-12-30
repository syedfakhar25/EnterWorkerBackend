<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }
    public function employee(){
        return $this->belongsTo(User::class,'employee_id');
    }
}
