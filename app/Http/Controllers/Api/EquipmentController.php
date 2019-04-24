<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Station;
use App\Company;
use App\EqChange;
use App\EqStop;
use App\EqData;
use App\EqRecovery;
use Carbon\Carbon;



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
     * 单一站点设备列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function equipmentList(Request $request)
    {

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

    	$now_time = Carbon::now()->startOfMonth();
    	$time = ($request->input('time')?$request->input('time'):$now_time);

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
