<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use DB;

class Cost extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 成本信息列表（含搜索）
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company [description]
     * @param    [type]     $number  [description]
     * @param    [type]     $start   [description]
     * @param    [type]     $end     [description]
     * @return   [type]              [description]
     */
    public static function list(Company $company, $number, $start, $end, $pre_page)
    {
    	$costs = self::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	if ($number) 
    	{
    		$costs = self::where('company_id', $company->id)->where('number', $number)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	}

    	if (!$costs) 
    	{
    		return [];
    	}

    	$costs = paginate_walk($costs, function($value, $key)
    	{
    		$data = 
    		[
    			'id' => $value->id,
    			'number' => $value->number,
    			'type' => $value->type,
    			'cost' => $value->cost,
    			'created_at' => $value->created_at,
    			'operator' => $value->admin->relname,
    		];
    		return $data;
    	});
    	return $costs;
    }

    /**成本管理添加
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company [description]
     * @param    Admin      $admin   [description]
     * @param    [type]     $type    [description]
     * @param    [type]     $cost    [description]
     */
    public static function add(Company $company, Admin $admin, $type, $cost)
    {
    	return DB::transaction(function() use ($company, $admin, $type, $cost)
        {
	    	$str = '92745535456171904303832514162534527289';
	    	$data = 
	    	[
	    		'company_id' => $company->id,
	    		'number' => 'YMCO'.substr(str_shuffle($str), 4, 16),
	    		'type' => $type,
	    		'cost' => $cost,
	    		'admin_id' => $admin->id,
	    	];
	    	return self::saveData($data);
	    });
    }
}
