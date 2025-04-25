<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';
    protected $fillable = ['user_id', 'role_id'];

    // Tell Laravel this model does not use an auto-incrementing ID
    public $incrementing = false;

    // Specify the composite primary key
    protected $primaryKey = ['user_id', 'role_id'];

    // Turn off timestamps
    public $timestamps = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault();
    }
}
