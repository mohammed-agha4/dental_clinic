<?php

namespace App\Models;

use App\Models\Tool;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];


    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

}
