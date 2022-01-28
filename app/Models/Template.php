<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
    protected $guarded=['*'];

    public function steps(){
        return $this->hasMany(TemplateStep::class,'template_id');
    }
}
