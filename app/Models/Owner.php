<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $primaryKey = 'owner_no';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'owner_no', 'f_name', 'l_name', 'street', 'city', 'postcode', 'tel_no'
    ];

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_no', 'owner_no');
    }
}