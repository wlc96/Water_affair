<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin;
use DB;

class AdminController extends Controller
{
	/**
	 * 添加管理员
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-04-08
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function adminAdd(Request $request)
    {
        return DB::transaction(function() use ($request)
        {
        	if(!$name = $request->input('name'))
        	{
        		return failure('用户名不能为空');
        	}

        	if(!$password = $request->input('password'))
        	{
        		return failure('密码不能为空');
        	}

        	if(!$company_id = $request->input('company_id'))
        	{
        		return failure('公司id不能为空');
        	}

        	if(!$phone = $request->input('phone'))
        	{
        		return failure('手机号不能为空');
        	}

        	if(!$email = $request->input('email'))
        	{
        		return failure('邮箱不能为空');
        	}

        	$admin = Admin::add($name, $password, $company_id, $phone, $email);

        	return success(['data' => $admin]);

        });

    }

    /**
     * 登陆接口
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function login(Request $request)
    {
        if(!$name = $request->input('name'))
        {
            return failure('用户名不能为空');
        }

        if(!$password = $request->input('password'))
        {
            return failure('密码不能为空');
        }

        if(!$admin = Admin::where('name', $name)->first()) 
        {
            return failure('该用户名不存在');
        }

        if(!$admin->checkPassword($password)) 
        {
            return failure('密码错误');
        }

        $day = 1;
        if($request->input('remember'))
        {
            $day = 30;
        }

        $access_token = $admin->login($day);
        return success(['data' => ['access_token' => $access_token]]);
    }

    /**
     * 获取用户信息接口
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-12
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function info(Request $request)
    {
        if (!$user = $request->admin)
        {
            return failure('请登录');
        }

        $user_info = $user->only('id', 'name', 'relname', 'phone', 'email', 'company_id');

        return success(['data' => $user_info]);
    }
}
