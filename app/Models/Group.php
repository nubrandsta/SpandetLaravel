<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $primaryKey = 'group_name';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['group_name', 'group_description'];
}