<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;
    protected $guarded = ['*'];

    public function employee(){
        return $this->belongsTo(User::class, 'designation_id');
    }
}
