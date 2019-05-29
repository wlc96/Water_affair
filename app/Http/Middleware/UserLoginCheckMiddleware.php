<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\User;

class UserLoginCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $access_token = $request->header('access-token');

        if(!$data = Cache::get($access_token))
        {
            return failure('请先登录', 401);
        }

        if(!$user = User::find($data['uid']))
        {
            return failure('用户不存在');
        }


        // 挂在用户数据
        $request->user = $user;

        $reponse = $next($request);

        User::refreshAccessToken($access_token);

        return $reponse;
    }
}
