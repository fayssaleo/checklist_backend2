<?php

namespace App\Modules\Fonction\Models;

use App\Modules\Department\Models\Department;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonction extends Model
{
    use HasFactory;
    protected $guarded=["id"];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',

    ];

}
