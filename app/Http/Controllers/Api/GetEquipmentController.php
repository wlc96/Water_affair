<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetEquipmentController extends Controller
{
    public function Hmac(Request $request)
    {
    	$params = $request->input('params');
    	$accessToken = $request->input('accessToken');

    	ksort($params);
    	$str = $accessToken;
    	foreach ($params as $key => $value) 
    	{
    		$str .= $key.(string)$value;
    	}
    	$str = hash_hmac("md5",$str,$accessToken,true);

    	$str = bin2hex($str);
    	return strtoupper($str);
    }
}
