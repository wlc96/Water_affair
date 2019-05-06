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
        $thisdata = self::where('company_id', $company->id)->where('month', $time)->first();
        if (!$thisdata) 
        {
            return [];
        }
    	$lasttime = $time->subMonth()->firstOfMonth();
    	$lastdata = self::where('company_id', $company->id)->where('month', $lasttime)->first();
        if (!$lastdata) 
        {
            return [];
        }

    	$data = [];
		$data['now'] = 
		[
			'id' => $thisdata->id,
			'should_num' => $thisdata->should_num,
			'real_num' => $thisdata->real_num,
			'completion_rate' => $thisdata->completion_rate,
			'accuracy_rate' => $thisdata->accuracy_rate,
			'mr_rate' => $thisdata->mr_rate,
			'ws_rate' => $thisdata->ws_rate,
		];

		$data['prev'] = 
		[
			'id' => $lastdata->id,
			'should_num' => $lastdata->should_num,
			'real_num' => $lastdata->real_num,
			'completion_rate' => $lastdata->completion_rate,
			'accuracy_rate' => $lastdata->accuracy_rate,
			'mr_rate' => $lastdata->mr_rate,
			'ws_rate' => $lastdata->ws_rate,
		];

    	return $data;
    }
}
