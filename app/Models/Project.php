<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Project extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function customer(){
    	return $this->belongsTo(User::class,'customer_id');
    }
}
