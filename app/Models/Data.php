<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids, HasFactory;

    protected $fillable = [
        'uploader',
        'group',
        'imgURI',
        'spandukCount',
        'lat',
        'long',
        'thoroughfare',
        'subLocality',
        'locality',
        'subAdmin',
        'adminArea',
        'postalCode',
        'deleted'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader', 'username');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group', 'group_name');
    }

    protected static function newFactory()
    {
        return \Database\Factories\DataFactory::new();
    }
}