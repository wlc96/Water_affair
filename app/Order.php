<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use App\EqChange;
use App\EqStop;
use App\EqRecovery;
use DB;

class Order extends Model
{
    use Traits\BaseModel, SoftDeletes;
    protected $casts = ['created_at' => 'string'];
    /**
     * 工单处理列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    Company    $company  [description]
     * @param    [type]     $number   [description]
     * @param    [type]     $start    [description]
     * @param    [type]     $end      [description]
     * @param    [type]     $pre_page [description]
     * @return   [type]               [description]
     */
    public static function list(Company $company, $number, $start, $end, $pre_page)
    {
    	$orders = self::where('company_id', $company->id)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	if ($number) 
    	{
    		$orders = self::where('company_id', $company->id)->where('order_num', $number)->whereBetween('created_at', [$start, $end])->paginate($pre_page);
    	}

    	$orders = paginate_walk($orders, function($value, $key)
    	{
    		$re = ('App\\'.($value->type))::where('id', $value->order_id)->first();
    		$data = 
    		[
    			'id' => $value->id,
    			'number' => $value->order_num,
    			'user_name' => $re->user->relname,
    			'phone' => $re->user->phone,
    			'type' => $value->type,
    			'created_at' => $re->created_at,
    			'admin_id' => $re->admin->relname,
    			'operation' => $re->servicing->name,
    			'status' => $re->status,
    		];

    		return $data;
    	});

    	return $orders;
    }

    /**
     * 工单编辑
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-18
     * @param    User       $user      [description]
     * @param    Servicing  $servicing [description]
     * @param    [type]     $status    [description]
     * @param    [type]     $explain   [description]
     * @return   [type]                [description]
     */
    public function edit(User $user, Servicing $servicing, $status, $explain)
    {
    	return DB::transaction(function() use ($user, $servicing, $status, $explain)
        {
	    	$data = ('App\\'.($this->type))::where('id', $this->order_id)->first();

	    	$data->user_id = $user->id;
	    	$data->servicing_id = $servicing->id;
	    	$data->status = $status;
	    	$data->explain = $explain;

	    	return $data->save();
	    });
    }
}
