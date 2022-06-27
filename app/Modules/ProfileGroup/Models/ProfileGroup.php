<?php

namespace App\Modules\ProfileGroup\Models;

use App\Modules\DamageType\Models\DamageType;
use App\Modules\Department\Models\Department;
use App\Modules\Equipment\Models\Equipment;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileGroup extends Model
{
    use HasFactory;
    protected $guarded=["id"];
    public function users(){
        return $this->belongsToMany(User::class,"user_profile_group")
            ->withTimestamps();
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function damageTypes()
    {
        return $this->hasMany(DamageType::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',

    ];

}
