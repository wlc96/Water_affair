<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Directory;
use App\Role;
use App\Admin;
use App\RoleDirectoryBind;
use DB;

class SystemController extends Controller
{
    
    /**
     * 目录列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function directoryList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$datas = Directory::all();

    	$data = [];
    	foreach ($datas as $key => $value) 
    	{
    		$data[] = $value->only('id', 'name');
    	}
    	return success(['data' => $data]);
    }

    /**
     * 增加角色
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function roleAdd(Request $request)
    {
    	$company = self::checkCompany($request);

    	if (!$name = $request->input('name')) 
    	{
    		return failure('请输入角色名');
    	}

    	if (Role::where('company_id', $company->id)->where('name', $name)->first()) 
    	{
    		return failure('该角色名已存在');
    	}

    	if (!$directory_ids = $request->input('directory_ids')) 
    	{
    		return failure('请选择目录');
    	}

    	if (!is_array($directory_ids)) 
    	{
    		return failure('目录需是数组');
    	}

    	return DB::transaction(function() use($company, $name, $directory_ids)
    	{
    		$role = Role::add($company, $name);
    		foreach ($directory_ids as $directory_id) 
    		{
    			$role_directory_bind = RoleDirectoryBind::add($company, $role, $directory_id);
    		}

    		return success(['data' => $rloe]);
    	});
    }

    /**
     * 添加管理员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function adminAdd(Request $request)
    {
    	$company = self::checkCompany($request);

    	if (!$name = $request->input('name')) 
    	{
    		return failure('请输入管理员账号');
    	}

    	if (Admin::where('company_id', $company->id)->where('name', $name)->first()) 
    	{
    		return failure('该管理员账号已存在');
    	}

    	if (!$password = $request->input('password')) 
    	{
    		return failure('请输入管理员密码');
    	}

    	if (!$relname = $request->input('relname')) 
    	{
    		return failure('请输入管理员真实姓名');
    	}

    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入手机号');
    	}

    	if (!$email = $request->input('email')) 
    	{
    		return failure('请输入邮箱');
    	}

    	if (!$role_ids = $request->input('role_ids')) 
    	{
    		return failure('请选择角色');
    	}

    	if (!is_array($role_ids)) 
    	{
    		return failure('角色必须是数组');
    	}
    	return DB::transaction(function() use($company, $name, $password, $relname, $phone, $email, $role_ids)
    	{
    		$data = Admin::add($company, $name, $password, $relname, $phone, $email, $role_ids);

    		return success(['data' =>$data]);
    	});
    	
    }

    /**
     * 编辑角色
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function roleEdit(Request $request)
    {
    	$company = self::checkCompany($request);
    	if (!$role_id = $request->input('role_id')) 
    	{
    		return failure('请选择角色id');
    	}

    	if (!$role = Role::where('id', $role_id)->first()) 
    	{
    		return failure('该角色不存在');
    	}

    	if (!$name = $request->input('name')) 
    	{
    		return failure('角色名不能为空');
    	}

    	if (Role::where('company_id', $company->id)->where('name', $name)->where('id', '!=', $role->id)->first()) 
    	{
    		return failure('该角色名已存在');
    	}

    	if (!$directory_ids = $request->input('directory_ids')) 
    	{
    		return failure('请选择目录');
    	}

    	if (!is_array($directory_ids)) 
    	{
    		return failure('目录需是数组');
    	}

    	return DB::transaction(function() use($role, $name, $directory_ids)
    	{
    		$data = $role->edit($name, $directory_ids);
    		return success(['data' =>$data]);
    	});
    }

    /**
     * 管理员列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-10
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function adminList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = Admin::list($company, $pre_page);

    	return success(['data' => $data]);
    }

    public function adminEdit(Request $request)
    {
    	$company = self::checkCompany($request);

    	if (!$admin_id = $request->input('admin_id')) 
    	{
    		return failure('请选择管理员');
    	}

    	if ($admin = Admin::where('id', $admin_id)->first()) 
    	{
    		return failure('该管理员不存在');
    	}

    	$password = ($request->input('password')?$request->input('password'):0);

    	if (!$relname = $request->input('relname')) 
    	{
    		return failure('请输入管理员真实姓名');
    	}

    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入手机号');
    	}

    	if (!$email = $request->input('email')) 
    	{
    		return failure('请输入邮箱');
    	}

    	if (!$role_ids = $request->input('role_ids')) 
    	{
    		return failure('请选择角色');
    	}

    	if (!is_array($role_ids)) 
    	{
    		return failure('角色必须是数组');
    	}

    	return DB::transaction(function() use($admin, $password, $relname, $phone, $email, $role_ids)
    	{
    		$data = $admin->edit($admin, $password, $relname, $phone, $email, $role_ids);

    		return success(['data' =>$data]);
    	});
    }


    /**
     * 检查公司是否存在
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-15
     * @return   [type]     [description]
     */
    public static function checkCompany($request)
    {
    	$admin = $request->admin;
    	if(!$company = Company::where('id', $admin->company_id)->first())
    	{
    		return failure('该公司不存在');
    	}

    	return $company;
    }
}