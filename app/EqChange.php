<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use DB;

class EqChange extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 拆换表列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Company    $company  [description]
     * @param    [type]     $type     [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $type, $pre_page)
    {
    	switch ($type) 
    	{
    		case 1:
    			$changes = self::getChange($company, 1, $pre_page);
    			$changes = paginate_walk($changes, function($value, $key)
		    	{
		    		$data = 
		    		[
		    			'id' => $value->id,
		    			'number' => $value->order_num,
		    			'user_num' => $value->user->number,
		    			'user_name' => $value->user->relname,
		    			'phone' => $value->user->phone,
		    			'station_name' => $value->station->name,
		    			'old_num' => $value->old_num,
		    			'new_num' => $value->new_num,
		    			'created_at' => $value->created_at,
		    		];
		    		return $data;
		    	});

		    	return $changes;
    			break;
    		case 2:
    			$changes = self::getChange($company, 2, $pre_page);
    			$changes = paginate_walk($changes, function($value, $key)
		    	{
		    		$data = 
		    		[
		    			'id' => $value->id,
		    			'number' => $value->order_num,
		    			'user_num' => $value->user->number,
		    			'user_name' => $value->user->relname,
		    			'phone' => $value->user->phone,
		    			'station_name' => $value->station->name,
		    			'move_time' => $value->move_time,
		    			'old_num' => $value->old_num,
		    			'start_num' => $value->start_num,
		    			'end_num' => $value->end_num,
		    			'created_at' => $value->created_at,
		    		];
		    		return $data;
		    	});
		    	return $changes;
    			break;
    		case 3:
    			$changes = self::getChange($company, 3, $pre_page);
    			$changes = paginate_walk($changes, function($value, $key)
		    	{
		    		$data = 
		    		[
		    			'id' => $value->id,
		    			'number' => $value->order_num,
		    			'user_num' => $value->user->number,
		    			'user_name' => $value->user->relname,
		    			'phone' => $value->user->phone,
		    			'station_name' => $value->station->name,
		    			'move_time' => $value->move_time,
		    			'old_num' => $value->old_num,
		    			'start_num' => $value->start_num,
		    			'end_num' => $value->end_num,
		    			'created_at' => $value->created_at,
		    		];
		    		return $data;
		    	});
		    	return $changes;
    			break;
    		case 4:
    			$changes = self::getChange($company, 4, $pre_page);
    			$changes = paginate_walk($changes, function($value, $key)
		    	{
		    		$data = 
		    		[
		    			'id' => $value->id,
		    			'number' => $value->order_num,
		    			'user_num' => $value->user->number,
		    			'user_name' => $value->user->relname,
		    			'phone' => $value->user->phone,
		    			'station_name' => $value->station->name,
		    			'move_time' => $value->move_time,
		    			'old_num' => $value->old_num,
		    			'new_num' => $value->new_num,
		    			'end_num' => $value->end_num,
		    			'created_at' => $value->created_at,
		    		];
		    		return $data;
		    	});
		    	return $changes;
    			break;
    	}
    }

    /**
     * 获取change数据
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    [type]     $company  [description]
     * @param    [type]     $type     [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function getChange($company, $type, $pre_page)
    {
    	$changes = self::where('company_id', $company->id)->where('type', $type)->paginate($pre_page);
		return $changes;
    }
}
