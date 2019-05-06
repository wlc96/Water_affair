<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Equipment;
use App\LadderWaterPrice;
use Illuminate\Support\Str;
class Station extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 我的站点信息,暂时版,表结构待修改 /w-c
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @param    Conpany    $company  [description]
     * @param    integer    $pre_page [description]
     * @return   [type]               [description]
     */
    public static function myStaion(Company $company, $name, $pre_page)
    {
    	$company->eq_sum = 0;
    	$company->big_eq_sum = 0;
    	$company->lit_eq_sum = 0;
    	$company->open_num = 0;
    	$company->close_num = 0;
    	$company->Off_line_num = 0;
    	$company->fault_num = 0;

    	$all_eq = self::where('company_id', $company->id)->get();
        if ($name) 
        {
            $all_eq = self::where('company_id', $company->id)->where('name', 'like', '%'.$name.'%')->get();
        }

    	if (!$all_eq) 
    	{
    		return [];
    	}

    	foreach ($all_eq as $key => $value) 
    	{
    		$num = $value->eqNum(0);
    		$big = $value->eqNum(1,1);
    		$lit = $value->eqNum(1,2);
    		$open = $value->eqNum(1,1);
    		$close = $value->eqNum(1,0);
    		$fault = $value->eqNum(1,2);
    		$Off_line = $value->eqNum(1,3);
    		$company->eq_sum = $company->eq_sum + $num;
    		$company->big_eq_sum = $company->big_eq_sum + $big;
    		$company->lit_eq_sum = $company->lit_eq_sum + $lit;
    		$company->open_num = $company->open_num + $open;
    		$company->close_num = $company->close_num + $close;
    		$company->fault_num = $company->fault_num + $fault;
    		$company->Off_line_num = $company->Off_line_num + $Off_line;
    	}

    	$stations = self::where('company_id', $company->id)->paginate($pre_page);

        if ($name) 
        {
            $stations = self::where('company_id', $company->id)->where('name', 'like', '%'.$name.'%')->paginate($pre_page);
        }

    	if (!$stations) 
    	{
    		return [];
    	}

    	$stations = paginate_walk($stations, function($value, $key)
    	{
    		$open = count(Equipment::where('station_id', $value->id)->where('status', 1)->get());
    		$close = count(Equipment::where('station_id', $value->id)->where('status', 0)->get());
    		$fault = count(Equipment::where('station_id', $value->id)->where('status', 2)->get());
    		$data = 
    		[
    			'id' => $value->id,
    			'name' => $value->name,
    			'sum_num' => $value->history_num,
    			'today_num' => ($value->today_num)/10,
    			'open_num' => $open,
    			'close_num' => $close,
    			'fault_num' => $fault,
    		];

    		return $data;
    	});

    	$company->list = $stations;
    	$data = $company->only('eq_sum', 'big_eq_sum', 'lit_eq_sum', 'open_num', 'close_num', 'fault_num', 'Off_line_num', 'list');
    	return $data;
    }

    /**
     * 单一站点详情
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @return   [type]     [description]
     */
    public function info()
    {
    	$this->eq_sum = $this->eqNum(0,0);
    	$this->big_eq_sum = $this->eqNum(1,1);
    	$this->lit_eq_sum = $this->eqNum(1,2);
    	$this->open_num = $this->eqNum(2,1);
    	$this->close_num = $this->eqNum(2,0);
    	$this->Off_line_num = $this->eqNum(2,3);
        $this->fault_num = $this->eqNum(2,2);
    	$this->pic = $url = Storage::url($this->pic);

    	$data = $this->only('name', 'linkman', 'phone', 'address', 'pic', 'business_hours', 'status', 'eq_sum', 'big_eq_sum', 'lit_eq_sum', 'open_num', 'close_num', 'Off_line_num', 'fault_num');
    	return $data;
    }

    /**
     * 编辑站点信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-24
     * @param    [type]     $name           [description]
     * @param    [type]     $address        [description]
     * @param    [type]     $phone          [description]
     * @param    [type]     $status         [description]
     * @param    [type]     $path           [description]
     * @param    [type]     $business_hours [description]
     * @return   [type]                     [description]
     */
    public function edit($name, $address, $phone, $status, $path, $business_hours)
    {
    	if ($path) 
    	{
    		$this->pic = $path;
    	}

    	$this->name = $name;
    	$this->address = $address;
    	$this->phone = $phone;
    	$this->status = $status;
    	$this->business_hours = $business_hours;

    	return $this->save();
    }

    /**
     * 站点阶梯水价
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-24
     * @return   [type]     [description]
     */
    public function xinfo()
    {
    	$water_price = LadderWaterPrice::where('company_id', $this->company->id)->where('station_id', $this->id)->first();
    	// return $water_price;

    	$data = $water_price->only('id', 'first_order', 'second_order', 'third_order');

    	return $data;
    }

    /**
     * 判断类型返回水表数量
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @param    [type]     $type [description]
     * @param    integer    $log  [description]
     * @return   [type]           [description]
     */
    public function eqNum($type, $log)
    {
    	if($type == 0)
    	{
    		return count(Equipment::where('station_id', $this->id)->get());
    	}
    	elseif($type == 1) 
    	{
    		return count(Equipment::where('station_id', $this->id)->where('type', $log)->get());
    	}
    	elseif($type == 2)
    	{
    		return count(Equipment::where('station_id', $this->id)->where('status', $log)->get());
    	}
    }
}
