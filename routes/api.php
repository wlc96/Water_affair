<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('ajax')->namespace('Api')->group(function()
{
	//添加管理员接口，供自己时用
	Route::post('admin/add', 'AdminController@adminAdd');

	//管理员登陆接口
	Route::post('admin/login', 'AdminController@login');

	//需要登陆验证
	Route::middleware('login')->group(function()
	{
		//管理员接口组
		Route::prefix('admin')->group(function()
		{
			//获取管理员信息接口
			Route::post('info', 'AdminController@info');

			//获取管理员权限接口
			Route::post('power', 'AdminController@power');
		});

		//城市信息接口，待定
		Route::post('cityTree', 'DistributionController@treeInfo');

		//设备管理接口组
		Route::prefix('equipment')->group(function()
		{
			//站点列表信息接口
			Route::post('station-list', 'EquipmentController@myStationList');

			//站点详细信息接口
			Route::post('station-info', 'EquipmentController@stationInfo');

			//站点信息修改接口
			Route::post('stationEdit', 'EquipmentController@stationInfoEdit');

			//站点阶梯水价信息接口
			Route::post('station-infoX', 'EquipmentController@stationInfoX');

			//站点阶梯水价调整接口
			Route::post('waterPrice', 'EquipmentController@waterPricesEdit');

			//设备列表接口
			Route::post('eqList', 'EquipmentController@eqList');

			//设备状态改变接口
			Route::post('eqEdit', 'EquipmentController@eqStatusEdit');

			//停水列表接口
			Route::post('eqchange', 'EquipmentController@equipmentChange');

			//停水列表接口
			Route::post('eqstop', 'EquipmentController@equipmentStop');

			//恢复供水列表接口
			Route::post('eqrecovery', 'EquipmentController@equipmentRecovery');

			//智能抄表列表接口
			Route::post('intelligent', 'EquipmentController@intelligentMR');
		});

		//经营管理接口组
		Route::prefix('Management')->group(function()
		{
			//公司账户信息接口
			Route::post('finance/company', 'ManagementController@companyFinancial');

			//公司订单列表接口
			Route::post('recharge/list', 'ManagementController@companyRecharge');

			//公司工单列表接口
			Route::post('workOrder/list', 'ManagementController@workOrderList');

			//公司添加工单接口
			Route::post('workOrder/add', 'ManagementController@workOrderAdd');

			//公司维修人员（操作员）列表接口
			Route::post('operator/list', 'ManagementController@operatorList');

			//公司成本列表接口
			Route::post('cost/list', 'ManagementController@costList');

			//公司成本添加接口
			Route::post('cost/add', 'ManagementController@costAdd');

			//公司发票列表接口
			Route::post('invoice/list', 'ManagementController@invoiceList');

			//公司发票添加接口
			Route::post('invoice/add', 'ManagementController@invoiceAdd');

			//公司价格管理列表接口
			Route::post('price/list', 'ManagementController@priceList');

		});

		Route::prefix('Operation')->group(function()
		{
			//故障处理列表接口
			Route::post('fault/list', 'OperationController@faultList');

			//故障编辑列表接口
			Route::post('fault/edit', 'OperationController@faultEdit');

			//工单处理列表接口
			Route::post('order/list', 'OperationController@orderList');

			//工单编辑列表接口
			Route::post('order/edit', 'OperationController@orderEdit');

			//添加巡检计划接口
			Route::post('patrolPlan/add', 'OperationController@patrolPlanAdd');

			//巡检计划列表接口
			Route::post('patrolPlan/list', 'OperationController@patrolPlanList');

			//编辑巡检计划接口
			Route::post('patrolPlan/edit', 'OperationController@patrolPlanEdit');

			//编辑巡检计划接口
			Route::post('patrolPlan/delete', 'OperationController@patrolPlanRemove');

			//巡检人列表接口
			Route::post('examiner/list', 'OperationController@examinerList');

			//添加巡检人接口
			Route::post('examiner/add', 'OperationController@examinerAdd');

			//编辑巡检人接口
			Route::post('examiner/edit', 'OperationController@examinerEdit');

			//编辑巡检人接口
			Route::post('examiner/delete', 'OperationController@examinerDelete');

		});

		Route::prefix('system')->group(function()
		{
			//目录列表接口
			Route::post('directory/list', 'SystemController@directoryList');

			//角色列表接口
			Route::post('role/list', 'SystemController@roleList');

			//增加角色接口
			Route::post('role/add', 'SystemController@roleAdd');

			//编辑角色接口
			Route::post('role/edit', 'SystemController@roleEdit');

			//删除角色接口
			Route::post('role/delete', 'SystemController@roleDelete');

			//增加管理员接口
			Route::post('admin/add', 'SystemController@adminAdd');

			//管理员列表接口
			Route::post('admin/list', 'SystemController@adminList');

			//编辑管理员接口
			Route::post('admin/edit', 'SystemController@adminEdit');

			//编辑管理员接口
			Route::post('admin/delete', 'SystemController@adminDelete');

		});
	});
});
