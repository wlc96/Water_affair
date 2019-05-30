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
Route::middleware('ajax')->namespace('WeChat')->group(function()
{
	//用户登陆接口
	Route::post('user/login', 'UserController@login');

	//获取验证码接口
	Route::post('user/getcode', 'UserController@sendCode');

	Route::middleware('user_login')->group(function()
	{
		//用户登陆接口
		Route::post('user/update', 'UserController@updateInfo');

		//用户登陆接口
		Route::post('user/info', 'UserController@info');

		//设备接口组
		Route::prefix('equipment')->group(function()
		{
			//设备列表接口
			Route::post('list', 'EquipmentController@list');

			//设备信息接口
			Route::post('dataInfo', 'EquipmentController@info');
		});

		//订单接口组
		Route::prefix('recharge')->group(function()
		{
			//获取管理员信息接口
			Route::post('list', 'RechargeController@list');

		});

		//故障接口组
		Route::prefix('workorder')->group(function()
		{
			//故障上报接口
			Route::post('add', 'WorkOrderController@store');

		});

		//发票接口组
		Route::prefix('invoice')->group(function()
		{
			//获取管理员信息接口
			Route::post('add', 'InvoiceController@addInvoice');

		});

		//支付接口组
		Route::prefix('pay')->group(function()
		{
			//城市信息接口
			Route::post('citys', 'PayController@waterCityList');

			//绑定水表接口
			Route::post('band_eq', 'PayController@bindEquipment');

			//绑定水表接口
			Route::post('group', 'PayController@groupList');

		});
	});

});
