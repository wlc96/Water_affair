<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Company;


class City extends Model
{
    use Traits\BaseModel;

    public static function myCityTree(Company $company)
    {
        $stations = Station::where('company_id', $company->id)->get();

        $citydatas = [];
        foreach ($stations as $station) 
        {
            foreach ($citydatas as $citydata) 
            {
                if ($station->t_city_id == $citydata['citys']['id']) 
                {
                     continue 2;
                }
            }
            $t_citys = self::where('id', $station->t_city_id)->first();
            $lstations = Station::where('t_city_id', $t_citys->id)->get();
           
            $eqs = 0;
            $childs = [];
            foreach ($lstations as $lstation) 
            {
                $eqs = $eqs + count(Equipment::where('station_id', $lstation->id)->get());
                foreach ($childs as $child) 
                {
                    if ($lstation->s_city_id == $child['citys']['id']) 
                    {
                        continue 2;
                    }
                }
                $s_citys = self::where('id', $lstation->s_city_id)->first();
                $rstations = Station::where('s_city_id', $s_citys->id)->get();
                $leqs = 0;
                $lchilds = [];
                foreach ($rstations as $rstation) 
                {
                    $leqs = $leqs + count(Equipment::where('station_id', $rstation->id)->get());
                    foreach ($lchilds as $lchild) 
                    {
                        if ($rstation->city_id == $lchild['citys']['id']) 
                        {
                            continue 2;
                        }
                    }
                    $f_citys = self::where('id', $rstation->city_id)->first();
                    $fstations = Station::where('city_id', $f_citys->id)->get();
                    $feqs = 0;
                    foreach ($fstations as $fstation) 
                    {
                        $feqs = $feqs + count(Equipment::where('station_id', $fstation->id)->get());
                    }

                    $lchilds[] = 
                    [
                        'citys' => $f_citys->only('id', 'name', 'lng', 'lat'),
                        'eqs' => $feqs,
                    ];
                }
                if (!$s_citys->lng) 
                {
                    $s_citys->lng = $t_citys->lng;
                    $s_citys->lat = $t_citys->lat;
                }
                $childs[] = 
                [
                    'citys' => $s_citys->only('id', 'name', 'lng', 'lat'),
                    'eqs' => $leqs,
                    'chid' => ($lchilds),
                ];
            }
            $citydatas[] = 
            [
                'citys' => $t_citys->only('id', 'name', 'lng', 'lat'),
                'eqs' => $eqs,
                'child' => $childs,
            ];
        }

        $data = ($citydatas);

        return $data;
    } 

    public static function cityTree()
    {
    	$tree = self::where('pid', 0)->get();

    	//return $tree;
    	$data  = self::allChild($tree,3);

    	return $data;
    }

    public static function allChild($tree, $deep)
    {
    	if($deep<=0)
    	{
    		return [];
    	}

    	$children = [];

    	foreach ($tree as $key => $value) 
    	{
			$v = self::where('pid', $value->id)->get();
			// if (count($v)) 
			// {
				$value->children = self::allChild($v, $deep-1);
				$children[] = $value->only('id', 'name', 'lng', 'lat', 'children');
			//}
    	}

    	foreach ($children as $key => $value) 
		{
			if ($value['children'] == null) 
			{
				unset($value['children']);
			}
		}
		return $children;
    }
}
