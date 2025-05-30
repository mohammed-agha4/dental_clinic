<?php

namespace App\Concerns;

use App\Models\Role;

trait HasRoles
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasAbility($ability)
    {
        // Cache the roles with abilities to avoid repeated queries
        static $rolesWithAbilities = null;

        if ($rolesWithAbilities === null) {
            $rolesWithAbilities = $this->roles()->with('abilities')->get();
        }

        foreach ($rolesWithAbilities as $role) {
            foreach ($role->abilities as $roleAbility) {
                if ($roleAbility->ability === $ability && $roleAbility->type === 'allow') {
                    return true;
                }
            }
        }

        return false;
    }
}
