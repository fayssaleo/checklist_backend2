<?php

namespace App\Modules\User\Models;

use App\Modules\Damage\Models\Damage;
use App\Modules\Fonction\Models\Fonction;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use  Notifiable, HasApiTokens ,HasFactory;

    protected $guarded=["id"];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    protected $hidden = [
        'password',
    ];

    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }

    public function profileGroups(){
        return $this->belongsToMany(ProfileGroup::class,"user_profile_group")
            ->withTimestamps();
    }

    public function declaredBys()
    {
        return $this->hasMany(Damage::class,"declaredBy_id");
    }

    public function confirmedBys()
    {
        return $this->hasMany(Damage::class,"confirmedBy_id");
    }

    public function closedBys()
    {
        return $this->hasMany(Damage::class,"closedBy_id");
    }

    public function revertedBys()
    {
        return $this->hasMany(Damage::class,"revertedBy_id");
    }

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',

    ];

}
