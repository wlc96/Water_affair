<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Servicing;
use App\Station;
use App\User;
use DB;

class WorkOrder extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 工单列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-15
     * @param    Company    $company  [description]
     * @param    integer    $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $pre_page = 10)
    {
    	$work_orders = self::where('company_id', $company->id)->paginate($pre_page);

    	if (!$work_orders) 
    	{
    		return [];
    	}

    	$work_orders = paginate_walk($work_orders, function($value, $key)
    	{
    		$data = 
    		[
    			'number' => $value->number,
    			'user_name' => $value->user->relname,
    			'phone' => $value->user->phone,
    			'type' => $value->type,
    			'created_at' => $value->created_at,
    			'operator' => $value->servicing->name,
    			'status' => $value->status,
    		];

    		return $data;

    	});

    	return $work_orders;
    } 

    /**
     * 故障列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Company    $company  [description]
     * @param    [type]     $number   [description]
     * @param    [type]     $start    [description]
     * @param    [type]     $end      [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function faultList(Company $company, $number, $start, $end, $pre_page)
    {
        $faultlists = self::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
        if ($number) 
        {
            $faultlists = self::where('company_id', $company->id)->where('number', $number)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
        }

        $faultlists = paginate_walk($faultlists, function($value, $key)
        {
            $data = 
            [
                'id' => $value->id,
                'number' => $value->number,
                'station_name' => $value->station->name,
                'eq_num' => $value->equipment_id,
                'type' => $value->type,
                'created_at' => $value->created_at,
                'operator' => $value->servicing->name,
                'phone' => $value->servicing->phone,
                'status' => $value->status,
            ];
            return $data;
        });

        return $faultlists;
    }

    /**
     * 添加工单
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company  [description]
     * @param    Servicing  $operator [description]
     * @param    User       $user     [description]
     * @param    [type]     $eq_num   [description]
     */
    public static function add(Company $company, User $user, Servicing $operator, $phone, $type, $eq_num)
    {
    	return DB::transaction(function() use ($company, $user, $operator, $phone, $type, $eq_num)
        {
	    	if (is_array($eq_num)) 
	    	{
	    		$eq_num = implode(',', $eq_num);
	    	}

	    	$str = '12836158129381723457812659103900129374518584912';
	    	$data = 
	    	[
	    		'company_id' => $company->id,
	    		'number' => 'YMZH'.substr(str_shuffle($str),4,16),
	    		'user_id' => $user->id,
	    		'link_phone' => $phone,
	    		'equipment_id' => $eq_num,
	    		'type' => $type,
	    		'servicing_id' => $operator->id,
	    	];

	    	return self::saveData($data);
	    });
    }


    /**
     * 编辑故障
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    [type]     $status  [description]
     * @param    [type]     $explain [description]
     * @return   [type]              [description]
     */
    public function edit($status, $explain)
    {
        return DB::transaction(function() use ($status, $explain)
        {
            $this->status = $status;
            $this->explain = $explain;

            $data = $this->save();

            return $data;
        });
    }
}
