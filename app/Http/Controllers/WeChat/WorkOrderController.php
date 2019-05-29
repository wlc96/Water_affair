<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Equipment;
use App\WorkOrder;
use DB;

class WorkOrderController extends Controller
{
    /**
     * 用户故障上报
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-25
     * todo 重复性校验
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function store(Request $request)
    {
    	$user = $request->user;

    	if (!$equipment_id = $request->input('equipment_id')) 
    	{
    		return failure('请输入水表号');
    	}

    	if (!$equipment = Equipment::where('id', $equipment_id)->first()) 
    	{
    		return failure('水表不存在');
    	}

    	if (!$type = $request->input('type')) 
    	{
    		return failure('请选择类型');
    	}

    	if (($type != 1) && ($type != 2)) 
    	{
    		return failure('类型输入有误，请重新输入');
    	}

    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入联系电话');
    	}

    	if (!$explain = $request->input('explain')) 
    	{
    		return failure('请输入说明');
    	}

    	return DB::transaction(function() use ($user, $equipment, $phone, $type, $explain)
    	{
    		$data = WorkOrder::wechatAdd($user, $equipment, $phone, $type, $explain);

    		return success(['data' => $data]);
    	});

    }
}
