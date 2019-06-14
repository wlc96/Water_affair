<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Station;
use App\WaterDatas;
use Carbon\Carbon;

class Equipment extends Model
{
    use Traits\BaseModel, SoftDeletes;
    protected $casts = ['created_at' => 'string'];
    public $incrementing=false;

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

    /**
     * 个人用户设备信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-24
     * @return   [type]     [description]
     */
    public function waterInfo()
    {
        $start_year = Carbon::now()->startOfYear();
        $end_year = Carbon::now()->endOfYear();
        $year_water_end = WaterDatas::where('equipment_id', $this->id)->whereBetween('created_at', [$start_year, $end_year])->orderBy('created_at', 'desc')->first();
        if ($year_water_end) 
        {
            $year_water_end = $year_water_end->all_num;
        }
        $year_water_start = WaterDatas::where('equipment_id', $this->id)->whereBetween('created_at', [$start_year, $end_year])->orderBy('created_at')->first();
        if ($year_water_start) 
        {
            $year_water_start = $year_water_start->all_num;
        }
        $year_water = $year_water_end-$year_water_start;

        $money = 0;

        if ($year_water <= 180) 
        {
            $money = $year_water*5;
        }
        else 
        {
            $a = $year_water-180;

            if ($a <= 80) 
            {
                $money = 900 + $a*7;
            }
            else
            {
                $b = $a-80;
                $money = 1460 + $b*9;
            }
        }

        $days = count(WaterDatas::where('equipment_id', $this->id)->whereBetween('created_at', [$start_year, $end_year])->get());
        if ($year_water) 
        {
            $day_water = $year_water/$days;
        }
        else
        {
            $day_water = 0;
        }

        $start_month = Carbon::now()->startOfMonth();
        $end_month = Carbon::now()->endOfMonth();
        $month_waters = WaterDatas::where('equipment_id', $this->id)->whereBetween('created_at', [$start_month, $end_month])->get();
        $evday_water = [];
        if ($month_waters) 
        {
            foreach ($month_waters as $month_water) 
            {
                $time = $month_water->created_at;
                $evday_water[] = 
                [
                    'day' => Carbon::parse($time)->toDateTimeString(),
                    'water' => $month_water->water_num,
                ];
            }
            
        }

        $data[] = 
        [
            'eq_id' => $this->id,
            'year_water' => $year_water,
            'year_money' => $money,
            'day_water' => $day_water,
            'surplus_water' => $this->surplus_water,
            'surplus_money' => $this->surplus_money,
            'evday_water' => $evday_water,
        ];

        return $data;
    }
}
