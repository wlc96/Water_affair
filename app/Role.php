<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\RoleAdminBind;
use App\RoleDirectoryBind;
use App\RoleAdminDirectoryBind;

class Role extends Model
{
    use Traits\BaseModel, SoftDeletes;
    protected $casts = ['created_at' => 'string'];
    /**
     * 角色列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Company    $company  [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $search, $pre_page)
    {
        $roles = self::where('company_id', $company->id)->paginate($pre_page);
        if ($search) 
        {
            $roles = self::where('company_id', $company->id)->where('name', 'like', '%'.$search.'%')->paginate($pre_page);
        }

        $roles = paginate_walk($roles, function($value, $key)
        {
            $directorys = RoleDirectoryBind::where('role_id', $value->id)->get();
            $name = [];
            foreach ($directorys as $directory) 
            {
                $name[] = $directory->directory->name;
            }
            $name = implode(',', $name);
            $data = 
            [
                'id' => $value->id,
                'type' => $value->type,
                'name' => $value->name,
                'directorys' => $name,
                'explain' => $value->explain,
                'created_at' => $value->created_at,
            ];

            return $data;
        });

        return $roles;
    }

    /**添加角色
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-19
     * @param    Company    $company [description]
     * @param    [type]     $name    [description]
     */
    public static function add(Company $company, $name, $explain)
    {
    	$data = 
    	[
            'company_id' => $company->id,
    		'explain' => $explain,
    		'name' => $name,
    	];

    	return self::saveData($data);
    }

    /**
     * 角色编辑
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $name          [description]
     * @param    [type]     $directory_ids [description]
     * @return   [type]                    [description]
     */
    public function edit($name, $directory_ids, $explain)
    {
        $this->name = $name;
    	$this->explain = $explain;

    	$this->save();

    	$data = $this->afterEdit($directory_ids);

    	return true;
    }

    /**
     * 编辑之后操作，增加和删除权限绑定
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $directory_ids [description]
     * @return   [type]                    [description]
     */
    public function afterEdit($directory_ids)
    {
    	$role_directory_binds = $this->getBindDirectory();
    	$old_directory_ids = [];
    	foreach ($role_directory_binds as $key => $value) 
    	{
    		if (!in_array($value->directory_id, $directory_ids)) 
    		{
    			$role_admin_directory_binds = RoleAdminDirectoryBind::where('role_directory_bind_id', $value->id)->get();
    			RoleAdminDirectoryBind::allDelete($role_admin_directory_binds);
    			$value->delete();
    		}
    		$old_directory_ids[] = $value->directory_id;
    	}

    	$role_admin_binds = $this->getBindAdmin();
    	foreach ($directory_ids as $directory_id) 
    	{
    		if (!in_array($directory_id, $old_directory_ids)) 
    		{
    			$role_directory_bind = RoleDirectoryBind::add($this->company, $this, $directory_id);
    			foreach ($role_admin_binds as $role_admin_bind) 
    			{
    				RoleAdminDirectoryBind::add($role_admin_bind, $role_directory_bind);
    			}
    		}
    	}

    	return true;
    }

    /**
     * 删除角色
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @return   [type]     [description]
     */
    public function remove()
    {
        $role_admin_binds = $this->getBindAdmin();
        foreach ($role_admin_binds as $role_admin_bind) 
        {
            $role_admin_bind->delete();
        }

        $role_directory_binds = $this->getBindDirectory();
        foreach ($role_directory_binds as $role_directory_bind) 
        {
            $role_directory_bind->delete();
        }

        $role_admin_directory_binds = $this->getBindAdminDirectory();

        foreach ($role_admin_directory_binds as $role_admin_directory_bind) 
        {
            $role_admin_directory_bind->delete();
        }

        $this->delete();

        return true;
    }

    /**
     * 角色绑定管理员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    Admin      $admin [description]
     * @return   [type]            [description]
     */
    public function bindAdmin(Admin $admin)
    {
    	$data = 
    	[
    		'company_id' => $admin->company_id,
    		'role_id' => $this->id,
    		'admin_id' => $admin->id,
    	];

    	return RoleAdminBind::saveData($data);
    }

    /**
     * 获取与角色绑定的管理员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @return   [type]     [description]
     */
    public function getBindAdmin()
    {
    	return RoleAdminBind::where('company_id', $this->company_id)->where('role_id', $this->id)->get();
    }

    /**
     * 获取与角色绑定的目录
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @return   [type]     [description]
     */
    public function getBindDirectory()
    {
    	return RoleDirectoryBind::where('company_id', $this->company_id)->where('role_id', $this->id)->get();
    }

    /**
     * 获取角色对应权限
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @return   [type]     [description]
     */
     public function getBindAdminDirectory()
    {
        return RoleAdminDirectoryBind::where('company_id', $this->company_id)->where('role_id', $this->id)->get();
    }
}
