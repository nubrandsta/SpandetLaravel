<?php

namespace App\Models;

use App\Models\Group;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = ['username', 'full_name', 'group', 'password'];
    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'password' => 'hashed',
    ];

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function group()
    {
        return $this->belongsTo(Group::class, 'group', 'group_name');
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
