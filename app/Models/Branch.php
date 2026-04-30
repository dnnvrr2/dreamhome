<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $primaryKey = 'branch_no';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'branch_no', 'street', 'area', 'city', 'postcode', 'tel_no', 'fax_no'
    ];

    public function properties()
    {
        return $this->hasMany(Property::class, 'branch_no', 'branch_no');
    }
}