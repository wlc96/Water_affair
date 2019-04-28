<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Station;
use App\Examiner;
use App\Admin;
use DB;

class InspectionPlan extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 添加巡检计划
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Company    $company  [description]
     * @param    Station    $station  [description]
     * @param    Examiner   $examiner [description]
     * @param    [type]     $name     [description]
     * @param    [type]     $type     [description]
     * @param    [type]     $xobject  [description]
     * @param    [type]     $cycle    [description]
     * @param    [type]     $time     [description]
     */
    public static function add(Company $company, Station $station, Examiner $examiner, Admin $admin, $name, $type, $xobject, $cycle, $time)
    {
    	return DB::transaction(function() use ($company, $station, $examiner, $admin, $name, $type, $xobject, $cycle, $time)
        {
	    	$str = '2546627284615500283802938657326';
	    	$data = 
	    	[
	    		'company_id' => $company->id,
	    		'num' => 'YMZH'.substr(str_shuffle($str), 4, 10),
	    		'name' => $name,
	    		'type' => $type,
	    		'cycle' => $cycle,
	    		'station_id' => $station->id,
	    		'xobject' => $xobject,
	    		'time' => $time,
	    		'examiner_id' => $examiner->id,
	    		'admin_id' => $admin->id,
	    	];

	    	return self::saveData($data);
	    });
    }

    /**
     * 巡检计划列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Company    $company  [description]
     * @param    [type]     $name     [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $name, $pre_page)
    {
    	$plans = self::where('company_id', $company->id)->where('name', 'like', '%'.$name.'%')->paginate($pre_page);
    	$plans = paginate_walk($plans, function($value, $key)
    	{
    		$data = 
    		[
    			'id' => $value->id,
    			'name' => $value->name,
    			'station' => $value->station->name,
    			'type' => $value->type,
    			'xobject' => $value->xobject,
    			'cycle' => $value->cycle,
    			'examiner' => $value->examiner->name,
    			'admin' => $value->admin->relname,
    			'time' => $value->time,
    		];

    		return $data;
    	});

    	return $plans;
    }

    /**
     * 编辑巡检计划
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Company    $company  [description]
     * @param    Station    $station  [description]
     * @param    Examiner   $examiner [description]
     * @param    Admin      $admin    [description]
     * @param    [type]     $name     [description]
     * @param    [type]     $type     [description]
     * @param    [type]     $xobject  [description]
     * @param    [type]     $cycle    [description]
     * @param    [type]     $time     [description]
     * @return   [type]               [description]
     */
    public function edit(Company $company, Station $station, Examiner $examiner, Admin $admin, $name, $type, $xobject, $cycle, $time)
    {
    	return DB::transaction(function() use ($company, $station, $examiner, $admin, $name, $type, $xobject, $cycle, $time)
        {
	    	$this->company_id = $company->id;
    		$this->name = $name;
    		$this->type = $type;
    		$this->cycle = $cycle;
    		$this->station_id = $station->id;
    		$this->xobject = $xobject;
    		$this->time = $time;
    		$this->examiner_id = $examiner->id;
    		$this->admin_id = $admin->id;

	    	return $this->save();
	    });
    }

    /**
     * 删除巡检计划
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Admin      $admin [description]
     * @return   [type]            [description]
     */
    public function remove(Admin $admin)
    {
    	return DB::transaction(function() use ($admin)
        {
	    	$this->admin->id = $admin->id;
	    	$this->save();

	    	return $this->delete();
	    });
    }
}
