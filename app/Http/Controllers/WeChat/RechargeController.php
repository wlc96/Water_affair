<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RechargeController extends Controller
{

    /**
     * 订单列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-24
     * @return   [type]     [description]
     */
    public function list(Request $request)
    {
    	$user = $request->user;

    	$data = $user->rechargeList();

    	return success(['data' => $data]);
    }
}
