<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\RoleAdminBind;
use App\RoleDirectoryBind;

class RoleAdminDirectoryBind extends Model
{
    use Traits\BaseModel, SoftDeletes;

    public static function add(RoleAdminBind $role_admin_bind, RoleDirectoryBind $role_directory_bind)
    {
    	$data = 
    	[
    		'company_id' => $role_admin_bind->company_id,
    		'role_id' => $role_admin_bind->role_id,
    		'admin_id' => $role_admin_bind->admin_id,
    		'directory_id' => $role_directory_bind->directory_id,
    		'role_admin_bind_id' => $role_admin_bind->id,
    		'role_directory_bind_id' => $role_directory_bind->id,
    		'can_add' => 1,
    		'can_edit' => 1,
    		'can_delete' => 1,
    	];

    	return self::saveData($data);
    }

    public static function allDelete($role_admin_directory_binds)
    {
    	foreach ($role_admin_directory_binds as $key => $value) 
    	{
    		$value->delete();
    	}

    	return true;
    }
}
