<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateTask extends Model
{
    use HasFactory;
    public $guarded=['*'];

    public function step(){
        return $this->belongsTo(TemplateStep::class,'step_id');
    }
}
