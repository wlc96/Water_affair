<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examiner extends Model
{
    use Traits\BaseModel, SoftDeletes;
    protected $casts = ['created_at' => 'string'];
    /**
     * 巡检人列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Company    $company  [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company,$pre_page)
    {
    	$examiners = self::where('company_id', $company->id)->where('status', 0)->paginate($pre_page);

    	$examiners = paginate_walk($examiners, function($value, $key)
        {
        	$data = 
        	[
        		'id' => $value->id,
        		'number' => $value->number,
        		'relname' => $value->relname,
        		'name' => $value->name,
        		'phone' => $value->phone,
        		'station' => $value->station->name,
        		'created_at' => $value->created_at,
        		'admin' => $value->admin->name,
        	];

        	return $data;
        });

        return $examiners;
    }

    /**
     * 增加巡检人员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Company    $company  [description]
     * @param    Admin      $admin    [description]
     * @param    Station    $station  [description]
     * @param    [type]     $relname  [description]
     * @param    [type]     $name     [description]
     * @param    [type]     $password [description]
     * @param    [type]     $phone    [description]
     */
    public static function add(Company $company, Admin $admin, Station $station, $relname, $name, $password, $phone)
    {
    	$str = '2546627284615500283802938657326';
    	$data = 
    	[
    		'name' => $name,
    		'password' => self::encrypt($password),
    		'relname' => $relname,
    		'phone' => $phone,
    		'number' => 'YMZH'.substr(str_shuffle($str), 4, 10),
    		'station_id' => $station->id,
    		'admin_id' => $admin->id,
    		'company_id' => $company->id,
    	];

    	return self::saveData($data);
    }

    /**
     * 编辑巡检人员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Admin      $admin    [description]
     * @param    Station    $station  [description]
     * @param    [type]     $relname  [description]
     * @param    [type]     $password [description]
     * @param    [type]     $phone    [description]
     * @return   [type]               [description]
     */
    public function edit(Admin $admin, Station $station, $relname, $password, $phone)
    {
    	$this->admin_id = $admin->id;
    	$this->station_id = $station->id;
    	$this->relname = $relname;
    	if ($password) 
    	{
    		$this->password = self::encrypt($password);
    	}
    	$this->phone = $phone;

    	return $this->save();
    }

    /**
     * 删除巡检人员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @return   [type]     [description]
     */
    public function remove()
    {
    	$this->status = 1;

    	return $this->save();
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
}
