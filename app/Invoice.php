<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use App\User;
use DB;

class Invoice extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 发票列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company  [description]
     * @param    [type]     $number   [description]
     * @param    [type]     $start    [description]
     * @param    [type]     $end      [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $number, $start, $end, $pre_page)
    {
    	$invoices = self::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	if ($number) 
    	{
    		$invoices = self::where('company_id', $company->id)->where('number', $number)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	}

    	if (!$invoices) 
    	{
    		return [];
    	}

    	$invoices = paginate_walk($invoices, function($value, $key)
    	{
    		$data = 
    		[
    			'id' => $value->id,
    			'number' => $value->number,
    			'user_name' => $value->user->relname,
    			'link_phone' => $value->link_phone,
                'type' => $value->type,
    			'status' => $value->status,
    			'cost' => $value->cost,
    			'created_at' => $value->created_at,
    			'operator' => $value->admin->relname,
    		];
    		return $data;
    	});
    	return $invoices;
    }

    /**
     * 添加发票
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-16
     * @param    Company    $company [description]
     * @param    Admin      $admin   [description]
     * @param    [type]     $type    [description]
     * @param    [type]     $cost    [description]
     */
    public static function add(Company $company, Admin $admin, User $user, $phone, $head_name, $cost, $type, $address)
    {
    	return DB::transaction(function() use ($company, $admin, $user, $phone, $head_name, $cost, $type, $address)
        {
	    	$str = '92745535456171904303832514162534527289';
	    	$data = 
	    	[
	    		'company_id' => $company->id,
	    		'number' => 'YMFP'.substr(str_shuffle($str), 4, 16),
	    		'user_id' => $user->id,
	    		'link_phone' => $phone,
	    		'head_name' => $head_name,
	    		'address' => $address,
	    		'type' => $type,
	    		'cost' => $cost,
	    		'admin_id' => $admin->id,
	    	];
	    	return self::saveData($data);
	    });
    }
}
