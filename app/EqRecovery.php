<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use DB;


class EqRecovery extends Model
{
	use Traits\BaseModel, SoftDeletes;
	protected $casts = ['created_at' => 'string'];
	/**
	 * 恢复列表
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-04-17
	 * @param    Company    $company  [description]
	 * @param    [type]     $pre_page [description]
	 * @return   [type]               [description]
	 */
    public static function list(Company $company, $pre_page)
    {
    	$recoveries = self::where('company_id', $company->id)->paginate($pre_page);
    	$recoveries = paginate_walk($recoveries, function($value, $key)
    	{
    		$data = 
    		[
    			'id' => $value->id,
    			'number' => $value->order_num,
    			'user_num' => $value->user->number,
    			'user_name' => $value->user->relname,
    			'phone' => $value->user->phone,
    			'station_name' => $value->station->name,
    			'created_at' => $value->created_at,
    			'type' => $value->explain,
    		];
    		return $data;
    	});

    	return $recoveries;
    }
}
