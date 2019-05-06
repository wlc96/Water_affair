<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Station;
use App\Company;
use App\EqChange;
use App\Equipment;
use App\EqStop;
use App\EqData;
use App\EqRecovery;
use Carbon\Carbon;
use App\LadderWaterPrice;
use DB;



class EquipmentController extends Controller
{

	/**
	 * 我的站点列表
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-04-13
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function myStationList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$data = Station::myStaion($company);

    	return success(['data' => $data]);
    }

    /**
     * 获取站点详情
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function stationInfo(Request $request)
    {
    	$company = self::checkCompany($request);

    	if(!$station_id = $request->input('station_id'))
    	{
    		return failure('请输入站点id');
    	}

    	if(!$station = Station::where('id', $station_id)->first())
    	{
    		return failure('该站点不存在');
    	}

    	$data = $station->info();
    	return success(['data' => $data]);
    }

    /**
     * 编辑站点信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-24
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function stationInfoEdit(Request $request)
    {
        $company = self::checkCompany($request);

        if(!$station_id = $request->input('station_id'))
        {
            return failure('请输入站点id');
        }

        if(!$station = Station::where('id', $station_id)->first())
        {
            return failure('该站点不存在');
        }

        if (!$name = $request->input('name')) 
        {
            return failure('请输入站点名');
        }

        if (!$address = $request->input('address')) 
        {
            return failure('请输入地址');
        }

        if (!$phone = $request->input('phone')) 
        {
            return failure('请输入站点联系电话');
        }

        $status = $request->input('status');
        if (!isset($status)) 
        {
            return failure('请选择状态');
        }

        if (!$business_hours = $request->input('business_hours')) 
        {
            return failure('请输入营业时间');
        }

        $path = 0;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) 
        {
            $photo = $request->file('photo');
            $extension = $photo->extension();
            $pname = md5($company->name.$company->id);
            $path = $photo->storeAs('photo', $pname.'.'.$extension);
        }

        return DB::transaction(function() use ($station, $name, $address, $phone, $status, $path, $business_hours)
        {
            $data =  $station->edit($name, $address, $phone, $status, $path, $business_hours);

            return success(['data' => $data]);
        });

    }

    /**
     * 水表列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function eqList(Request $request)
    {
        $company = self::checkCompany($request);

        if(!$station_id = $request->input('station_id'))
        {
            return failure('请输入站点id');
        }

        if(!$station = Station::where('id', $station_id)->first())
        {
            return failure('该站点不存在');
        }

        $pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

        $data = Equipment::list($station, $pre_page);
        return success(['data' => $data]);
    }

    /**
     * 设备状态编辑
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function eqStatusEdit(Request $request)
    {
        $company = self::checkCompany($request);

        if(!$equipment_id = $request->input('equipment_id'))
        {
            return failure('请输入设备id');
        }

        if(!$equipment = Equipment::where('id', $equipment_id)->first())
        {
            return failure('该设备不存在');
        }

        if (!$operate = $request->input('operate')) 
        {
            return failure('请选择操作');
        }

        $data = $equipment->statusEdit($operate);

        return success(['data' => $data]);
    }

    /**
     * 站点阶梯水价
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-24
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function stationInfoX(Request $request)
    {
        $company = self::checkCompany($request);

        if(!$station_id = $request->input('station_id'))
        {
            return failure('请输入站点id');
        }

        if(!$station = Station::where('id', $station_id)->first())
        {
            return failure('该站点不存在');
        }

        $data = $station->xinfo();
        return success(['data' => $data]);
    }

    /**
     * 阶梯水价调整
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-24
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function waterPricesEdit(Request $request)
    {
        $company = self::checkCompany($request);

        if (!$water_price_id = $request->input('water_price_id')) 
        {
            return failure('请选择阶梯水价');
        }

        if (!$water_price = LadderWaterPrice::where('id', $water_price_id)->first()) 
        {
            return failure('该阶梯水价不存在');
        }

        if (!$first_order = $request->input('first_order')) 
        {
            return failure('请输入一阶水价');
        }

        if (!$second_order = $request->input('second_order')) 
        {
            return failure('请输入二阶水价');
        }

        if (!$third_order = $request->input('third_order')) 
        {
            return failure('请输入三阶水价');
        }

        return DB::transaction(function() use ($water_price, $first_order, $second_order, $third_order)
        {
            $data = $water_price->edit($first_order, $second_order, $third_order);

            return success(['data' => $data]);
        });

    }

    /**
     * 拆换表列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function equipmentChange(Request $request)
    {
    	$company = self::checkCompany($request);
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	if (!$type = $request->input('type')) 
    	{
    		return failure('请选择查询类型');
    	}

    	$data = EqChange::list($company, $type, $pre_page);

    	return success(['data' => $data]);
    }

    /**
     * 停水列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function equipmentStop(Request $request)
    {
    	$company = self::checkCompany($request);
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = EqStop::list($company, $pre_page);
    	return success(['data' => $data]);
    }

    /**
     * 恢复供水
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function equipmentRecovery(Request $request)
    {
    	$company = self::checkCompany($request);
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = EqRecovery::list($company, $pre_page);
    	return success(['data' => $data]);
    }

    /**
     * 智能抄表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function intelligentMR(Request $request)
    {
    	$company = self::checkCompany($request);

    	$now_time = Carbon::now()->subMonth()->firstOfMonth();
    	$time = $now_time;
        if ($time = $request->input('time')) 
        {
            $time = Carbon::parse($time);
        }
        
    	$data = EqData::list($company, $time);
    	return success(['data' => $data]);
    }

    /**
     * 检查公司是否存在
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-15
     * @return   [type]     [description]
     */
    public static function checkCompany($request)
    {
    	$admin = $request->admin;
    	if(!$company = Company::where('id', $admin->company_id)->first())
    	{
    		return failure('该公司不存在');
    	}

    	return $company;
    }
}
