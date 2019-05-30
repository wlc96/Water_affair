<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaterCity extends Model
{
    use Traits\BaseModel;

    /**
     * 城市列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-29
     * @return   [type]     [description]
     */
    public static function list()
    {
    	$initials = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

    	$data = [];
    	foreach ($initials as $initial)
    	{
    		$citys = self::where('initials', $initial)->get();

    		$cityc = [];
    		$hot = [];
    		$city_hots = self::where('hot', 1)->get();
    		foreach ($city_hots as $city_hot) 
    		{
    			$companys = WaterCompany::where('water_city_id', $city_hot->id)->get();

    			$companyc = [];
    			if (!$companys) 
    			{
    				$company[] = [];
    			}
    			else
    			{
    				foreach ($companys as $company) 
    				{
    					$companyc[] = $company->name;
    				}
    			}
    			$hot[] = 
    			[
    				'name' => $city_hot->name,
    				'companys' => $companyc,
    			];
    		}
    		foreach ($citys as $city) 
    		{
    			$companys = WaterCompany::where('water_city_id', $city->id)->get();

    			$companyc = [];
    			if (!$companys) 
    			{
    				$company[] = [];
    			}
    			else
    			{
    				foreach ($companys as $company) 
    				{
    					$companyc[] = $company->name;
    				}
    			}
    			$cityc[] = 
    			[
    				'name' => $city->name,
    				'companys' => $companyc,
    			];
    		}
    		$data[] = 
    		[
    			'hot' => $hot,
    			'initial' => $initial,
    			'citys' => $cityc,
    		];
    	}

    	return $data;
    }
}
