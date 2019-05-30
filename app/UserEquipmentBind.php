<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEquipmentBind extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 用户绑定水表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-30
     * @param    WaterCompany $water_company [description]
     * @param    Equipment    $equipment     [description]
     * @param    UserGroup    $group         [description]
     * @param    [type]       $hu_name       [description]
     */
    public static function add(User $user, WaterCompany $water_company, Equipment $equipment, UserGroup $group, $hu_name)
    {
    	$data = 
    	[
    		'user_id' => $user->id,
    		'equipment_id' => $equipment->id,
    		'user_group_id' => $group->id,
    		'water_company_id' => $water_company->id,
    		'hu_name' => $hu_name,
    	];
    	$res = self::saveData($data);

    	return $res;
    }
}
