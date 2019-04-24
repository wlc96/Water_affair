<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Role;

class Admin extends Model
{
    use Traits\BaseModel, SoftDeletes;
	
    /**
     * 添加管理员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    Company    $company  [description]
     * @param    [type]     $name     [description]
     * @param    [type]     $password [description]
     * @param    [type]     $relname  [description]
     * @param    [type]     $phone    [description]
     * @param    [type]     $email    [description]
     * @param    [type]     $role_ids [description]
     */
    public static function add(Company $company, $name, $password, $relname, $phone, $email, $role_ids)
    {
        $data = 
        [
            'name' => $name,
            'relname' => $relname,
            'password' => self::encrypt($password),
            'company_id' => $company->id,
            'phone' => $phone,
            'email' => $email,
        ];

        $admin = self::saveData($data);

        $admin->afterAdd($role_ids);

        return true;
    }

    public function afterAdd($role_ids)
    {
        foreach ($role_ids as $role_id) 
        {
            $role = Role::where('id', $role_id)->first();
            $role_admin_bind = $role->bindAdmin($this);
            $role_directory_binds = $role->getBindDirectory();
            foreach ($role_directory_binds as $role_directory_bind) 
            {
                $all_bind = RoleAdminDirectoryBind::add($role_admin_bind, $role_directory_bind);
            }
        }

        return true;
    }

    /**
     * 管理员列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    Company    $company  [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $pre_page)
    {
        $admins = self::where('company_id', $company->id)->paginate($pre_page);

        $admins = paginate_walk($admins, function($value, $key)
        {
            $role_admin_binds = RoleAdminBind::where('admin_id', $value->id)->get();
            $role_name = [];
            foreach ($role_admin_binds as $role_admin_bind) 
            {
                $role_name[] = $role_admin_bind->role->name;
            }
            $role_name = implode(',', $role_name);
            $data = 
            [
                'name' => $value->name,
                'relname' => $value->relname,
                'phone' => $value->phone,
                'email' => $value->email,
                'created_at' => $value->created_at,
                'role_name' => $role_name,
            ];

            return $data;
        });

        return $admins;
    }

    /**
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    Admin      $admin    [description]
     * @param    [type]     $password [description]
     * @param    [type]     $relname  [description]
     * @param    [type]     $phone    [description]
     * @param    [type]     $email    [description]
     * @param    [type]     $role_ids [description]
     * @return   [type]               [description]
     */
    public function edit(Admin $admin, $password, $relname, $phone, $email, $role_ids)
    {
        if ($password) 
        {
            $this->password = $password;
        }

        $this->relname = $relname;
        $this->phone = $phone;
        $this->email = $email;
        
        $this->save();

        $this->afterEdit($role_ids);

        return true;
    }

    public function afterEdit($role_ids)
    {
        $role_admin_binds = RoleAdminBind::where('admin_id', $this->id)->get();
        foreach ($role_admin_binds as $role_admin_bind) 
        {
            // if (!in_array($role_admin_bind->role_id, $)) 
            // {
            //     # code...
            // }
        }
    }

    /**
     * 登陆
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    integer    $day [description]
     * @return   [type]          [description]
     */
    public function login($day = 1)
    {
        $expires_at = Carbon::now()->addDays($day);
        
        $access_token = self::getSessionId();

        Cache::put($access_token, ['id' => $this->id, 'day' => $day], $expires_at);

        return $access_token;
    }

    /**
     * 获取SessionId
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @return   [type]     [description]
     */
    public static function getSessionId()
    {
        return sha1(uniqid('', true).Str::random(25).microtime(true));
    }

    /**
     * 密码校验
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    [type]     $password [description]
     * @return   [type]               [description]
     */
    public function checkPassword($password)
    {
        return $this->password == self::encrypt($password);
    }

    /**
     * 密码加密
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    [type]     $password [description]
     * @return   [type]               [description]
     */
    private static function encrypt($password)
    {
        return md5(config('common.user_password_key') . '|' . $password);
    }

    /**
     * 刷新登陆时间
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-12
     * @param    [type]     $access_token [description]
     * @return   [type]                   [description]
     */
    public static function refreshAccessToken($access_token)
    {
        if($data = Cache::get($access_token))
        {
            $expires_at = Carbon::now()->addDays($data['day']);
            Cache::put($access_token, $data, $expires_at);
        }

        return true;
    }
}
