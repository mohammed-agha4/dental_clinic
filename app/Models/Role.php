<?php

namespace App\Models;

use App\Models\RoleAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];


    public function abilities() {
        return $this->hasMany(RoleAbility::class);
    }


    public static function createWithAbilities(Request $request) {

        DB::beginTransaction();
        try{

            $role = Role::create([
                'name' => $request->post('name'),
            ]);

            foreach ($request->post('abilities') as $ability => $value) {
                RoleAbility::create([
                    'role_id' => $role->id,
                    'ability' => $ability,
                    'type' => $value,
                ]);
            }

            DB::commit();

        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $role;
    }



    public function updateWithAbilities(Request $request) {

        DB::beginTransaction();
        try{

            $this->update([
                'name' => $request->post('name'),
            ]);

            foreach ($request->post('abilities') as $ability => $value) {
                RoleAbility::updateOrCreate([
                    'role_id' => $this->id,
                    'ability' => $ability,
                ], [
                    'type' => $value,
                ]);
            }

            DB::commit();

        }catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this;
    }
}
