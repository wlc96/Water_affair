<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use DB;
use Carbon\Carbon;


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

    /**
     * 新增订单
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-06-14
     * //todo 阶梯水价分站点
     * @param    User       $user      [description]
     * @param    Eloquent   $equipment [description]
     * @param    [type]     $sum       [description]
     * @param    [type]     $type      [description]
     */
    public static function add(User $user, Equipment $equipment, $sum, $type)
    {
        return DB::transaction(function() use ($user, $equipment, $sum, $type)
        {
            $str = '26351824092183721983701293801924720640218730219730101928301724546';

            $this_time = time();
            $num = 'YMZH'.$this_time.substr(str_shuffle($str),3,10);

            $user_equipment_bind = UserEquipmentBind::where('user_id', $user->id)->where('equipment_id', $equipment->id)->first();
            if (!$user_equipment_bind) 
            {
                return failure('用户未绑定水表');
            }

            $water_company = $user_equipment_bind->water_company;
            $data = 
            [
                'company_id' => $equipment->station->company->id,
                'water_company_id' => $water_company->id,
                'number' => $num,
                'user_id' => $user->id,
                'station_id' => $equipment->station->id,
                'equipment_id' => $equipment->id,
                'sum' => $sum,
                'type' => $type,
            ];

            $recharge = self::saveData($data);
            return $recharge;
        });
    }

}
