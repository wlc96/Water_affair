<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Equipment;
use App\WaterDatas;
use App\UserEquipmentBind;
use DB;

class User extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 添加用户
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-28
     * @param    [type]     $phone [description]
     */
    public static function add($phone)
    {
        return DB::transaction(function() use ($phone)
        {
            $a = '9123091823456746435809123123123118429182';
            $a = str_shuffle($a);
            $data = 
            [
                'number' => 'YMU'.substr($a,3,10),
                'phone' => $phone,
            ];

            return self::saveData($data);
        });
    }

    /**
     * 设备信息列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-22
     * @return   [type]     [description]
     */
    public function equipmentList()
    {
        $eqs = UserEquipmentBind::where('user_id', $this->id)->get();
        if (!$eqs) 
        {
            return [];
        }

        $data = [];
        $start_year = Carbon::now()->startOfYear();
        $end_year = Carbon::now()->endOfYear();
        foreach ($eqs as $eq) 
        {
            $year_water_end = WaterDatas::where('equipment_id', $eq->equipment_id)->whereBetween('created_at', [$start_year, $end_year])->orderBy('created_at', 'desc')->first();
            if ($year_water_end) 
            {
                $year_water_end = $year_water_end->all_num;
            }
            $year_water_start = WaterDatas::where('equipment_id', $eq->equipment_id)->whereBetween('created_at', [$start_year, $end_year])->orderBy('created_at')->first()->all_num;
            if ($year_water_start) 
            {
                $year_water_start = $year_water_start->all_num;
            }
            $year_water = $year_water_end-$year_water_start;
            $data[] = 
            [
                'eq_id' => $eq->equipment_id,
                'year_water' => $year_water,
                'surplus_water' => $eq->equipment->surplus_water,
                'surplus_money' => $eq->equipment->surplus_money,
            ];
        }

        return $data;
    }

    /**
     * 订单列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-24
     * @return   [type]     [description]
     */
    public function rechargeList()
    {
        $recharges = Recharge::where('user_id', $this->id)->get();
        if (!$recharges) 
        {
            return [];
        }

        $times = [];
        foreach ($recharges as $recharge) 
        {
            $month = $recharge->created_at->startOfMonth()->toDateString();
            array_push($times, $month);
            $times = array_unique($times);
        }

        $data = [];
        foreach ($times as $time) 
        {
            $start_day = Carbon::parse($time)->startOfMonth();
            $end_day = Carbon::parse($time)->endOfMonth();
            $day_recharges = Recharge::where('user_id', $this->id)->whereBetween('created_at', [$start_day, $end_day])->get();
            $rdata = [];
            foreach ($day_recharges as $day_recharge) 
            {
                $rdata[] = 
                [
                    'id' => $day_recharge->id,
                    'time' => $day_recharge->created_at->toDateTimeString(),
                    'equipment_id' => $day_recharge->equipment_id,
                    'money' => $day_recharge->sum,
                ];
            }
            rsort($rdata);
            $data[] = 
            [
                'time' => $time,
                'recharge' => $rdata,
            ];

        }

        rsort($data);
        return $data;
    }

    /**
     * 改变用户信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-25
     * @return   [type]     [description]
     */
    public function change($path, $name)
    {
        if ($path) 
        {
            $this->icon = $path;
        }

        if ($name) 
        {
            $this->relname = $name;
        }

        return $this->save();
    }

    /**
     * 登陆
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    integer    $day [description]
     * @return   [type]          [description]
     */
    public function login($day = 7)
    {
        $expires_at = Carbon::now()->addDays($day);
        
        $access_token = self::getSessionId();

        Cache::put($access_token, ['uid' => $this->id, 'day' => $day], $expires_at);

        return $access_token;
    }

    /**
     * 获取SessionId
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @return   [type]     [description]
     */
    public static function getSessionId()
    {
        return sha1(uniqid('', true).Str::random(25).microtime(true));
    }

    /**
     * 密码校验
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    [type]     $password [description]
     * @return   [type]               [description]
     */
    public function checkPassword($password)
    {
        return $this->password == self::encrypt($password);
    }

    /**
     * 密码加密
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-11
     * @param    [type]     $password [description]
     * @return   [type]               [description]
     */
    private static function encrypt($password)
    {
        return md5(config('common.user_password_key') . '|' . $password);
    }

    /**
     * 刷新登陆时间
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-12
     * @param    [type]     $access_token [description]
     * @return   [type]                   [description]
     */
    public static function refreshAccessToken($access_token)
    {
        if($data = Cache::get($access_token))
        {
            $expires_at = Carbon::now()->addDays($data['day']);
            Cache::put($access_token, $data, $expires_at);
        }

        return true;
    }
}
