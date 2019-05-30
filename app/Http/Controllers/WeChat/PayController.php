<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WaterCity;
use App\WaterCompany;
use App\UserGroup;
use App\Equipment;
use App\UserEquipmentBind;
use DB;

class PayController extends Controller
{
	/**
	 * 获取水务公司城市列表
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-05-29
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function waterCityList(Request $request)
    {
    	$data = WaterCity::list();

    	return success(['data' => $data]);
    }

    /**
     * todo 户名校验
     * 用户绑定水表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-29
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function bindEquipment(Request $request)
    {
    	$user = $request->user;
    	if (!$water_company_id = $request->input('water_company_id')) 
    	{
    		return failure('请输入缴费单位id');
    	}

    	if (!$water_company = WaterCompany::where('id', $water_company_id)->first()) 
    	{
    		return failure('该水务公司不存在');
    	}

    	if (!$group_id = $request->input('group_id')) 
    	{
    		return failure('请选择组id');
    	}

    	if (!$group = UserGroup::where('id', $group_id)->first()) 
    	{
    		return failure('该分组不存在');
    	}

    	if (!$equipment_id = $request->input('equipment_id')) 
    	{
    		return failure('请输入水表号');
    	}

    	if (!$equipment = Equipment::where('id', $equipment_id)->first()) 
    	{
    		return failure('该水表不存在');
    	}

    	if (!$hu_name = $request->input('hu_name')) 
    	{
    		return failure('请输入户名');
    	}

    	return DB::transaction(function() use ($user, $water_company, $equipment, $group, $hu_name)
    	{
    		$data = UserEquipmentBind::add($user, $water_company, $equipment, $group, $hu_name);
    		
    		return success(['data' => $data]);
    	});
    }

    /**
     * 水表分组列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-30
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function groupList(Request $request)
    {
    	$datas = UserGroup::all();

    	return success(['data' => $datas]);
    }
}
