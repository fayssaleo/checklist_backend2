<?php

namespace App\Modules\Damage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function damage(){
        return $this->belongsTo(Damage::class);
    }
}
