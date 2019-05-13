<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\User;
use App\Invoice;
use App\Cost;
use App\Station;
use App\Recharge;
use App\WorkOrder;
use App\Servicing;
use App\PriceManagement;

class ManagementController extends Controller
{
    
	/**
	 * 公司财务信息
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-04-13
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function companyFinancial(Request $request)
    {
    	$company = self::checkCompany($request);

    	$data = $company->financialInfo();
    	return success(['data' => $data]);
    }
    /**
     * 公司订单列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function companyRecharge(Request $request)
    {
    	
    	$company = self::checkCompany($request);

    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = Recharge::companyList($company, $pre_page);

    	return success(['data' => $data]);
    }

    /**
     * 工单列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-15
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function workOrderList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):2);
        $number = ($request->input('number')?$request->input('number'):0);
        $start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
        $end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');

    	$data = WorkOrder::list($company, $number, $start, $end, $pre_page);

    	return success(['data' => $data]);
    }

    /**
     * 操作员列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-15
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function operatorList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$operators = Servicing::where('company_id', $company->id)->get();
    	if (!$operators) 
    	{
    		return success(['data' => []]);
    	}

    	$data = [];
    	foreach ($operators as $key => $value) 
    	{
    		$data[] = $value->only('id', 'name');
    	}
    	return success(['data' => $data]);
    }

    /**
     * 添加工单
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function workOrderAdd(Request $request)
    {
    	$company = self::checkCompany($request);
    	if (!$user_name = $request->input('user_name')) 
    	{
    		return failure('请输入用户名');
    	}

    	if (!$user = User::where('name', $user_name)->first()) 
    	{
    		return failure('该用户不存在');
    	}

        if (!$station_id = $request->input('station_id')) 
        {
            return failure('请输入站点id');
        }

        if (!$station = Station::where('id', $station_id)->first()) 
        {
            return failure('该站点不存在');
        }

    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入联系电话');
    	}

    	if (!$type = $request->input('type')) 
    	{
    		return failure('请选择问题类型');
    	}

    	if (!$operator_id = $request->input('operator_id')) 
    	{
    		return failure('请选择操作员');
    	}

    	if (!$operator = Servicing::where('id', $operator_id)->first()) 
    	{
    		return failure('操作员不存在');
    	}

    	if (!$eq_num = $request->input('eq_num')) 
    	{
    		return failure('请输入水表编号');
    	}

    	$data = WorkOrder::add($company, $station, $user, $operator, $phone, $type, $eq_num);

    	return success(['data' => $data]);
    }

    /**
     * 成本管理列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function costList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$number = ($request->input('number')?$request->input('number'):0);
    	$start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
    	$end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = Cost::list($company, $number, $start, $end, $pre_page);

    	return success(['data' => $data]);
    }

    /**
     * 成本管理添加
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function costAdd(Request $request)
    {
    	$company = self::checkCompany($request);

    	if (!$type = $request->input('type')) 
    	{
    		return failure('请选择类型');
    	}

    	if (!$cost = $request->input('cost')) 
    	{
    		return failure('请输入金额');
    	}

    	$admin = $request->admin;

    	$data = Cost::add($company, $admin, $type, $cost);

    	return success(['data' => $data]);
    }

    /**
     * 发票列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function invoiceList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$number = ($request->input('number')?$request->input('number'):0);
    	$start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
    	$end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = Invoice::list($company, $number, $start, $end, $pre_page);

    	return success(['data' => $data]);
    }


    /**
     * 添加发票
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function invoiceAdd(Request $request)
    {
    	$company = self::checkCompany($request);

    	if (!$user_name = $request->input('user_name')) 
    	{
    		return failure('请输入用户名');
    	}

    	if (!$user = User::where('name', $user_name)->first()) 
    	{
    		return failure('该用户不存在');
    	}

    	if (!$phone = $request->input('phone')) 
    	{
    		return failure('请输入联系电话');
    	}

    	if (!$head_name = $request->input('head_name')) 
    	{
    		return failure('请输入抬头名称');
    	}

    	if (!$cost = $request->input('cost')) 
    	{
    		return failure('请输入发票金额');
    	}

    	if (!$type = $request->input('type')) 
    	{
    		return failure('请选择发票类型');
    	}

    	if (!$address = $request->input('address')) 
    	{
    		return failure('请输入邮寄地址');
    	}

    	$admin = $request->admin;
    	$data = Invoice::add($company, $admin, $user, $phone, $head_name, $cost, $type, $address);

    	return success(['data' => $data]);
    }

    /**
     * 价格管理列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function priceList(Request $request)
    {
    	$company = self::checkCompany($request);

    	$number = ($request->input('number')?$request->input('number'):0);
    	$start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
    	$end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = PriceManagement::list($company, $number, $start, $end, $pre_page);

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
