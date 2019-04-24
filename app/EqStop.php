<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use DB;

class EqStop extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 停水列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company  [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $pre_page)
    {
    	$stops = self::where('company_id', $company->id)->paginate($pre_page);
    	$stops = paginate_walk($stops, function($value, $key)
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
    			'type' => $value->type,
    		];
    		return $data;
    	});

    	return $stops;
    }
}
