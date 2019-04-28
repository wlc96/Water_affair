<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\City;
use App\Company;
use DB;

class DistributionController extends Controller
{
	/**
	 * 公司城市树接口
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-04-13
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function treeInfo(Request $request)
    {
    	$admin = $request->admin;

    	if(!$company = Company::where('id', $admin->company_id)->first())
    	{
    		return failure('该公司不存在');
    	}

    	$data = City::myCityTree($company);

    	return success(["data" => $data]);
    }
}
