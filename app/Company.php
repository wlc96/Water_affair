<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Recharge;
use Carbon\Carbon;

class Company extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 财务信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-04-13
     * @return   [type]     [description]
     */
    public function financialInfo()
    {
    	$recharges = Recharge::where('company_id', $this->id)->get();

    	if (!$recharges) 
    	{
    		return [];
    	}

    	$num = 0.0000;
    	foreach ($recharges as $key => $value) 
    	{
    		$num = $num + $value->sum;
    	}

    	$startOfMonth = Carbon::now()->startOfMonth();
    	$endOfMonth = Carbon::now()->endOfMonth();
    	$mon_num = 0.0000;
    	$m_recharges = Recharge::where('company_id', $this->id)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
    	if ($m_recharges) 
    	{
    		foreach ($m_recharges as $key => $value) 
    		{
    			$mon_num = $mon_num + $value->sum;
    		}
    	}
        $this->sum_money = $num;
    	$this->account_balance = $num;
    	$this->mon_money = $mon_num;
    	$data = $this->only('account_balance', 'sum_money', 'mon_money');
    	return $data;
    }

}
