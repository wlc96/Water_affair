<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInvoice extends Model
{
    use Traits\BaseModel, SoftDeletes;

    /**
     * 添加发票
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-28
     * @param    Recharge   $recharge  [description]
     * @param    [type]     $type      [description]
     * @param    [type]     $number    [description]
     * @param    [type]     $head_type [description]
     * @param    [type]     $head      [description]
     * @param    [type]     $name      [description]
     * @param    [type]     $phone     [description]
     * @param    [type]     $email     [description]
     * @param    [type]     $explain   [description]
     * @param    [type]     $address   [description]
     */
    public static function add(Recharge $recharge, $type, $number, $head_type, $head, $name, $phone, $email, $explain, $address)
    {
    	$data = 
    	[
    		'recharge_id' => $recharge->id,
    		'type' => $type,
    		'head_type' => $head_type,
    		'head' => $head,
    		'number' => $number,
    		'money' => $recharge->sum,
    		'explain' => $explain,
    		'name' => $name,
    		'phone' => $phone,
    		'email' => $email,
    		'address' => $address,
    	];

    	$data = self::saveData($data);
    	return $data;
    }
}
