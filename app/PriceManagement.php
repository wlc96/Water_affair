<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use DB;

class PriceManagement extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 价格管理列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company  [description]
     * @param    [type]     $number   [description]
     * @param    [type]     $start    [description]
     * @param    [type]     $end      [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $number, $start, $end, $pre_page)
    {
    	$prices = self::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	if ($number) 
    	{
    		$prices = self::where('company_id', $company->id)->where('number', $number)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	}

    	if (!$prices) 
    	{
    		return [];
    	}

    	$prices = paginate_walk($prices, function($value, $key)
    	{
    		$data = 
    		[
    			'id' => $value->id,
    			'number' => $value->number,
    			'station_name' => $value->station->name,
    			'type' => $value->type,
    			'original_price' => $value->original_price,
    			'existing_price' => $value->existing_price,
    			'created_at' => $value->created_at,
    			'operator' => $value->admin->relname,
    		];
    		return $data;
    	});
    	return $prices;
    }
}
