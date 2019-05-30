<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Mrgoon\AliSms\AliSms;
use App\User;
use Cache;
use Carbon\Carbon;

class UserController extends Controller
{

    /**
     * 发送验证码
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-28
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function sendCode(Request $request)
    {
        $day = 5;
        $num = 0;
        if (!$phone = $request->input('phone')) 
        {
            return failure('手机号不能为空');
        }

        if ($phone_cache = Cache::get($phone)) 
        {
            if ($phone_cache['expires_at'] >= (Carbon::now()->addMinutes($day-1))) 
            {
                return failure('请稍后再试');
            }

            $num = $phone_cache['num'];
            if (($phone_cache['expires_at']->startOfDay()->toDateString()) < Carbon::now()->startOfDay()->toDateString()) 
            {
                $num = 0;
            }

            if ($num >= 10) 
            {
                return failure('今日获取次数过多，请明日重试');
            }

        }
        $str = '28274523621983691870219830124672164216498217038120974835781387210';
        $yan = substr(str_shuffle($str), 3, 6);
        $data = AliSms::sendSms($phone, 'SMS_166476643', ['code'=> $yan]);
        $num = $num + 1;

        $expires_at = Carbon::now()->addMinutes($day);

        Cache::put($phone, ['yan' => $yan, 'expires_at' => $expires_at, 'num' => $num], $expires_at);

        return success();

    }

	/**
	 * 用户登陆
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-05-22
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function login(Request $request)
    {
    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入手机号');
    	}

        if (!$code = $request->input('code')) 
        {
            return failure('请输入验证码');
        }

        if (!$phone_cache = Cache::get($phone)) 
        {
            return failure('验证码失效，请重新获取');
        }

        if ($phone_cache['yan'] != $code) 
        {
            return failure('验证码错误，请重新输入');
        }

        if (!$user = User::where('phone', $phone)->first()) 
        {
            $user = User::add($phone);
        }


        $day = 7;
        if($request->input('remember'))
        {
            $day = 30;
        }

        $access_token = $user->login($day);
        return success(['data' => ['access_token' => $access_token]]);
    }

    /**
     * 用户信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-30
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function info(Request $request)
    {
        $user = $request->user;

        return success(['data' => $user]);
    }
    /**
     * 修改个人信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-25
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function updateInfo(Request $request)
    {
        $user = $request->user;

        $path = 0;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) 
        {
            $photo = $request->file('photo');
            $extension = $photo->extension();
            $pname = md5($user->name.$user->id);
            $path = $photo->storeAs('user_icon', $pname.'.'.$extension);
        }

        $name = $request->input('name')?$request->input('name'):'';

        $data = $user->change($path, $name);
        $url = Storage::url('user_icon/a2473e01a702c1ef07b5a47eab631442.png');
        return success(['data' => $url]);
    }
}
