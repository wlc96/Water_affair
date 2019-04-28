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

class Event extends Model
{
    use Traits\BaseModel, SoftDeletes;

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
    			'number' => $value->number,
    			'type' => $value->type,
    			'station' => $re->station->name,
    			'man' => $value->report_user_id,
    			'created' => $value->created_at,
    			'status' => $re->status,

    		];

    		return $data;
    	});

    	return $orders;
    }

    /**
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-27
     * @param    [type]     $status [description]
     * @return   [type]             [description]
     */
    public function edit($status)
    {
    	return DB::transaction(function() use ($status)
        {
	    	$data = ('App\\'.($this->type))::where('id', $this->order_id)->first();

	    	$data->status = $status;
	    	return $data->save();
	    });
    }
}
