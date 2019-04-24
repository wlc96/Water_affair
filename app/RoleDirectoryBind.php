<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Role;
use App\Directory;

class RoleDirectoryBind extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**增加角色目录绑定
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-19
     * @param    Company    $company   [description]
     * @param    Role       $role      [description]
     * @param    Directory  $directory [description]
     */
    public static function add(Company $company, Role $role, $directory_id)
    {
		$data = 
		[
			'company_id' => $company->id,
			'role_id' => $role->id,
			'directory_id' => $directory_id,
		];

    	return self::saveData($data);
    }
}
