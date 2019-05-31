<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserInvoice;
use App\Recharge;
use App\City;
use DB;

class InvoiceController extends Controller
{
	/**
	 * 添加发票
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-05-24
	 * @param    Request    $request [description]
	 */
    public function addInvoice(Request $request)
    {
    	$email = '';
    	$address = '';
    	$number = '';
    	if (!$recharge_id = $request->input('recharge_id')) 
    	{
    		return failure('请选择订单');
    	}

    	if (UserInvoice::where('recharge_id', $recharge_id)->first())
    	{
    		return failure('该订单已开具发票');
    	}

    	if (!$recharge = Recharge::where('id', $recharge_id)->first()) 
    	{
    		return failure('订单不存在');
    	}

    	if (!$type = $request->input('type')) 
    	{
    		return failure('请选择发票类型');
    	}

    	if ($type == 2) 
    	{
    		if (!$address = $request->input('address')) 
    		{
    			return failure('请输入地址');
    		}
    	}
    	else
    	{
    		if (!$email = $request->input('email')) 
    		{
    			return failure('请输电子邮箱');
    		}
    	}

    	$explain = $request->input('explain');//备注
    	if (!$head_type = $request->input('head_type')) 
    	{
    		return failure('请选择抬头类型');
    	}

    	if ($head_type == 2) 
    	{
    		if (!$number = $request->input('number')) 
    		{
    			return failure('请输入税号');
    		}
    	}

    	if (!$head = $request->input('head')) 
    	{
    		return failure('请输入抬头');
    	}

    	if (!$name = $request->input('name')) 
    	{
    		return failure('请输入收件人姓名');
    	}

    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入收件人手机号');
    	}

    	return DB::transaction(function() use ($recharge, $type, $number, $head_type, $head, $name, $phone, $email, $explain, $address)
    	{
    		$data = UserInvoice::add($recharge, $type, $number, $head_type, $head, $name, $phone, $email, $explain, $address);

    		return success(['data' => $data]);
    	});

    }

    /**
     * 城市联动列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-31
     * @param    Recharge   $request [description]
     * @return   [type]              [description]
     */
    public function citys(Request $request)
    {
    	$city_id = 0;


    	if ($request->input('city_id')) 
    	{
    		$city_id = $request->input('city_id');
    	}

    	$data = City::lianList($city_id);

    	return success($data);
    }
}
