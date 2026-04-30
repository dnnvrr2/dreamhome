<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $primaryKey = 'property_no';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'property_no', 'street', 'area', 'city', 'postcode',
        'type', 'rooms', 'rent', 'is_available', 'owner_no', 'branch_no'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_no', 'owner_no');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_no', 'branch_no');
    }
}