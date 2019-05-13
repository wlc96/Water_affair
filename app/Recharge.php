<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;


class Recharge extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 公司订单列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @param    Company    $company  [description]
     * @param    integer    $pre_page [description]
     * @return   [type]               [description]
     */
    public static function companyList(Company $company, $number, $start, $end, $pre_page = 10)
    {
    	$recharges = self::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
        if ($number) 
        {
            $recharges = self::where('company_id', $company->id)->where('number', $number)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
        }
    	if (!$recharges) 
    	{
    		return [];
    	}

    	$recharges = paginate_walk($recharges, function($value, $key)
    	{

    		$data = 
    		[
    			'number' => $value->number,
    			'station_name' => $value->station->name,
    			'user_num' => $value->user->number,
    			'water_quantity' => $value->water_quantity,
    			'num' => $value->sum,
    			'type' => $value->type,
    			'created_at' => $value->created_at,
    		];

    		return $data;
    	});

    	return $recharges;
    }
}
