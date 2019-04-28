<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAdminBind extends Model
{
    use Traits\BaseModel, SoftDeletes;

    public static function add(Company $company, Admin $admin, $role_id)
    {
    	$data = 
		[
			'company_id' => $company->id,
			'role_id' => $role_id,
			'admin_id' => $admin->id,
		];

    	return self::saveData($data);
    }
}
