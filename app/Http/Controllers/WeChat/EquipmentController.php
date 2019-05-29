<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Equipment;

class EquipmentController extends Controller
{
	/**
	 * 个人用户设备列表
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-05-24
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function list(Request $request)
    {
    	$user = $request->user;

    	$data = $user->equipmentList();

    	return success(['data' => $data]);
    }

    /**
     * 设备详情
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-24
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function info(Request $request)
    {
    	if (!$equipment_id = $request->input('equipment_id')) 
    	{
    		return failure('请选择设备');
    	}

    	if (!$equipment = Equipment::where('id', $equipment_id)->first()) 
    	{
    		return failure('该设备不存在');
    	}

    	$data = $equipment->waterInfo();

    	return success(['data' => $data]);
    }
}
