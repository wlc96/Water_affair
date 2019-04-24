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
    	return self::cityTree();
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
