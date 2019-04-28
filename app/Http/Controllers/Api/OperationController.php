<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WorkOrder;
use App\Company;
use App\EqChange;
use App\EqStop;
use App\EqRecovery;
use App\Servicing;
use App\Order;
use App\User;
use App\Examiner;
use App\Station;
use App\InspectionPlan;
use App\Event;
use DB;



class OperationController extends Controller
{
    
    /**
     * 故障列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function faultList(Request $request)
    {
    	$company = self::checkCompany($request);
    	$number = ($request->input('number')?$request->input('number'):0);
    	$start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
    	$end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = WorkOrder::faultList($company, $number, $start, $end, $pre_page);

    	return success(['data' => $data]);
    }

    /**
     * 编辑故障信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function faultEdit(Request $request)
    {
    	$company = self::checkCompany($request);
    	if (!$order_id = $request->input('order_id')) 
    	{
    		return failure('请输入工单id');
    	}

    	if (!$order = WorkOrder::where('id', $order_id)->first()) 
    	{
    		return failure('该工单不存在');
    	}

    	if (!$status = $request->input('status')) 
    	{
    		return failure('请选择状态');
    	}

    	if (!$explain = $request->input('explain')) 
    	{
    		return failure('请添加描述');
    	}

    	$data = $order->edit($status, $explain);

    	return success(['data' => $data]);
    }

    /**
     * 工单处理列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-17
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function orderList(Request $request)
    {
    	$company = self::checkCompany($request);
    	$number = ($request->input('number')?$request->input('number'):0);
    	$start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
    	$end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');
    	$pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

    	$data = Order::list($company, $number, $start, $end, $pre_page);

    	return success(['data' => $data]);
    }

    /**
     * 工单编辑
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function orderEdit(Request $request)
    {
        $company = self::checkCompany($request);
        if (!$order_id = $request->input('order_id')) 
        {
            return failure('请输入工单id');
        }

        if (!$order = Order::where('id', $order_id)->first()) 
        {
            return failure('该工单不存在');
        }

        if (!$user_name = $request->input('user_name')) 
        {
            return failure('请输入用户名');
        }

        if (!$user = User::where('name', $user_name)->first()) 
        {
            return failure('用户不存在');
        }

        if (!$servicing_id = $request->input('servicing_id')) 
        {
            return failure('请选择派发人员');
        }

        if (!$servicing = Servicing::where('id', $servicing_id)->first()) 
        {
            return failure('该人员不存在');
        }

        if (!$status = $request->input('status')) 
        {
            return failure('请选择状态');
        }

        $explain = ($request->input('explain')?$request->input('explain'):'');

        $data = $order->edit($user, $servicing, $status, $explain);

        return success(['data' => $data]);
    }

    /**删除工单
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-27
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function orderDelete(Request $request)
    {
        $company = self::checkCompany($request);
        if (!$order_id = $request->input('order_id')) 
        {
            return failure('请输入工单id');
        }

        if (!$order = Order::where('id', $order_id)->first()) 
        {
            return failure('该工单不存在');
        }

        return DB::transaction(function() use($order)
        {
            $data = $order->delete();

            return success(['data' => $data]);
        });
    }

    /**
     * 事件列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-27
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function eventList(Request $request)
    {
        $company = self::checkCompany($request);
        $number = ($request->input('number')?$request->input('number'):0);
        $start = ($request->input('start')?$request->input('start'):'1990-01-01 00:00:00');
        $end = ($request->input('end')?$request->input('end'):'2050-01-01 00:00:00');
        $pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

        $data = Event::list($company, $number, $start, $end, $pre_page);

        return success(['data' => $data]);
    }

    /**
     * 事件编辑
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-27
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function eventEdit(Request $request)
    {
        $company = self::checkCompany($request);
        
        if (!$event_id = $request->input('event_id')) 
        {
            return failure('请输入事件id');
        }

        if (!$event = Event::where('id', $event_id)->first()) 
        {
            return failure('该事件不存在');
        }

        if (!$status = $request->input('status')) 
        {
            return failure('请选择状态');
        }

        $data = $event->edit($status);

        return success(['data' => $data]);
    }

    /**
     * 删除事件
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-27
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function eventDelete(Request $request)
    {
        $company = self::checkCompany($request);
        if (!$event_id = $request->input('event_id')) 
        {
            return failure('请输入事件id');
        }

        if (!$event = Event::where('id', $event_id)->first()) 
        {
            return failure('该事件不存在');
        }

        return DB::transaction(function() use($event)
        {
            $data = $event->delete();

            return success(['data' => $data]);
        });
    }

    /**
     * 增加巡检计划
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function patrolPlanAdd(Request $request)
    {
        $company = self::checkCompany($request);
        $admin = $request->admin;
        if (!$name = $request->input('name')) 
        {
            return failure('请输入计划名');
        }

        if (InspectionPlan::where('company_id', $company->id)->where('name', $name)->first()) 
        {
            return failure('该计划名已存在');
        }

        if (!$station_id = $request->input('station_id')) 
        {
            return failure('请选择站点');
        }

        if (!$station = Station::where('id', $station_id)->first()) 
        {
            return failure('站点不存在');
        }

        if (!$type = $request->input('type')) 
        {
            return failure('请选择巡检方式');
        }

        if (!$xobject = $request->input('xobject')) 
        {
            return failure('请输入巡检对象');
        }

        if (!$cycle = $request->input('cycle')) 
        {
            return failure('请输入巡检周期');
        }

        if (!$examiner_id = $request->input('examiner_id')) 
        {
            return failure('请选巡检人员');
        }

        if (!$examiner = Examiner::where('id', $examiner_id)->first()) 
        {
            return failure('该巡检员不存在');
        }

        if (!$time = $request->input('time')) 
        {
            return failure('请输入巡检时间');
        }

        $data = InspectionPlan::add($company, $station, $examiner, $admin, $name, $type, $xobject, $cycle, $time);

        return success(['data' => $data]);
    }

    /**
     * 巡检计划列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function patrolPlanList(Request $request)
    {
        $company = self::checkCompany($request);
        $name = ($request->input('name')?$request->input('name'):'');
        $pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

        $data = InspectionPlan::list($company, $name, $pre_page);

        return success(['data' => $data]);
    }

    /**
     * 修改巡检计划
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function patrolPlanEdit(Request $request)
    {
        $company = self::checkCompany($request);
        $admin = $request->admin;
        if (!$plan_id = $request->input('plan_id')) 
        {
            return failure('请选择巡检计划');
        }

        if (!$plan = InspectionPlan::where('id', $plan_id)->first()) 
        {
            return failure('巡检计划不存在');
        }

        if (!$name = $request->input('name')) 
        {
            return failure('请输入计划名');
        }

        if (InspectionPlan::where('company_id', $company->id)->where('name', $name)->where('id', '!=', $plan->id)->first())
        {
            return failure('该计划名已存在');
        }

        if (!$station_id = $request->input('station_id')) 
        {
            return failure('请选择站点');
        }

        if (!$station = Station::where('id', $station_id)->first()) 
        {
            return failure('站点不存在');
        }

        if (!$type = $request->input('type')) 
        {
            return failure('请选择巡检方式');
        }

        if (!$xobject = $request->input('xobject')) 
        {
            return failure('请输入巡检对象');
        }

        if (!$cycle = $request->input('cycle')) 
        {
            return failure('请输入巡检周期');
        }

        if (!$examiner_id = $request->input('examiner_id')) 
        {
            return failure('请选巡检人员');
        }

        if (!$examiner = Examiner::where('id', $examiner_id)->first()) 
        {
            return failure('该巡检员不存在');
        }

        if (!$time = $request->input('time')) 
        {
            return failure('请输入巡检时间');
        }

        $data = $plan->edit($company, $station, $examiner, $admin, $name, $type, $xobject, $cycle, $time);

        return success(['data' => $data]);
    }

    /**
     * 删除巡检计划
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-26
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function patrolPlanRemove(Request $request)
    {
        $company = self::checkCompany($request);
        $admin = $request->admin;
        if (!$plan_id = $request->input('plan_id')) 
        {
            return failure('请选择巡检计划');
        }

        if (!$plan = InspectionPlan::where('id', $plan_id)->first()) 
        {
            return failure('巡检计划不存在');
        }

        $data = $plan->remove($admin);

        return success(['data' => $data]);
    }

    /**
     * 巡检人列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function examinerList(Request $request)
    {
        $company = self::checkCompany($request);

        $pre_page = ($request->input('pre_page')?$request->input('pre_page'):10);

        $data = Examiner::list($company, $pre_page);

        return success(['data' => $data]);
    }

    /**
     * 添加巡检人
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function examinerAdd(Request $request)
    {
        $company = self::checkCompany($request);
        $admin = $admin = $request->admin;

        if (!$relname = $request->input('relname')) 
        {
            return failure('请输入巡检人员名');
        }

        if (!$phone = $request->input('phone')) 
        {
            return failure('请输入巡检人员电话');
        }

        if (!$name = $request->input('name')) 
        {
            return failure('请输入巡检人员登陆名');
        }

        if (Examiner::where('company_id', $company->id)->where('name', $name)->first()) 
        {
            return failure('该用户名已存在');
        }

        if (!$password = $request->input('password')) 
        {
            return failure('请输入巡检人员密码');
        }

        if (!$station_id = $request->input('station_id')) 
        {
            return failure('请选择负责站点');
        }

        if (!$station = Station::where('company_id', $company->id)->where('id', $station_id)->first()) 
        {
            return failure('该站点不存在');
        }

        return DB::transaction(function() use($company, $admin, $station, $relname, $name, $password, $phone)
        {
            $data = Examiner::add($company, $admin, $station, $relname, $name, $password, $phone);

            return success(['data' => $data]);
        });
    }

    public function examinerEdit(Request $request)
    {
        $company = self::checkCompany($request);
        $admin = $admin = $request->admin;

        if (!$examiner_id = $request->input('examiner_id')) 
        {
            return failure('请选择巡检人员');
        }

        if (!$examiner = Examiner::where('id', $examiner_id)->first()) 
        {
            return failure('该巡检人员不存在');
        }

        if (!$relname = $request->input('relname')) 
        {
            return failure('请输入巡检人员名');
        }

        if (!$phone = $request->input('phone')) 
        {
            return failure('请输入巡检人员电话');
        }

        $password = '';
        $password = $request->input('password');

        if (!$station_id = $request->input('station_id')) 
        {
            return failure('请选择负责站点');
        }

        if (!$station = Station::where('company_id', $company->id)->where('id', $station_id)->first()) 
        {
            return failure('该站点不存在');
        }

        return DB::transaction(function() use($examiner, $admin, $station, $relname, $password, $phone)
        {
            $data = $examiner->edit($admin, $station, $relname, $password, $phone);

            return success(['data' => $data]);
        });
    }

    /**
     * 删除巡检人员
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-25
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function examinerDelete(Request $request)
    {
        $company = self::checkCompany($request);

        if (!$examiner_id = $request->input('examiner_id')) 
        {
            return failure('请选择巡检人员');
        }

        if (!$examiner = Examiner::where('id', $examiner_id)->first()) 
        {
            return failure('该巡检人员不存在');
        }

        return DB::transaction(function() use($examiner)
        {
            $data = $examiner->remove();

            return success(['data' => $data]);
        });
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
