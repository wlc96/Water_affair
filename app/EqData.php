<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;
use App\Admin;
use Carbon\Carbon;
use DB;


class EqData extends Model
{
    use Traits\BaseModel, SoftDeletes;

    public static function list(Company $company, $time)
    {
    	$nexttime = Carbon::now()->subMonth()->firstOfMonth();
    	$nextdatas = self::where('company_id', $company->id)->where('month', $nexttime)->get();
    	$datas = self::where('company_id', $company->id)->where('month', $time)->get();

    	$data = [];
    	foreach ($datas as $key => $value) 
    	{
    		$data['now'] = 
    		[
    			'id' => $value->id,
    			'should_num' => $value->should_num,
    			'real_num' => $value->real_num,
    			'completion_rate' => $value->completion_rate,
    			'accuracy_rate' => $value->accuracy_rate,
    			'mr_rate' => $value->mr_rate,
    			'ws_rate' => $value->ws_rate,
    		];
    	}

    	foreach ($nextdatas as $key => $value) 
    	{
    		$data['prev'] = 
    		[
    			'id' => $value->id,
    			'should_num' => $value->should_num,
    			'real_num' => $value->real_num,
    			'completion_rate' => $value->completion_rate,
    			'accuracy_rate' => $value->accuracy_rate,
    			'mr_rate' => $value->mr_rate,
    			'ws_rate' => $value->ws_rate,
    		];
    	}

    	return $data;
    }
}
