<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Station;

class Equipment extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 设备列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Company    $company [description]
     * @return   [type]              [description]
     */
    public static function list(Station $station, $pre_page)
    {
    	$equipments = self::where('station_id', $station->id)->paginate($pre_page);

    	$equipments = paginate_walk($equipments, function($value, $key)
    	{
    		$data = 
    		[
    			'number' => $value->eq_num,
    			'caliber' => $value->equipment_type->caliber,
    			'status' => $value->status,
    			'total_water' => $value->total_water,
    			'last_month' => $value->last_month,
    			'this_month' => $value->this_month,
    			'surplus_water' => $value->surplus_water,
    			'surplus_money' => $value->surplus_money,
    		];

    		return $data;
    	});

    	return $equipments;
    }

    /**
     * 改变水表状态
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    [type]     $operate [description]
     * @return   [type]              [description]
     */
    public function statusEdit($operate)
    {
    	$this->status = 1;

    	return $this->save();
    }
}
