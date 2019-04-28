<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LadderWaterPrice extends Model
{
    use Traits\BaseModel, SoftDeletes;

    public function edit($first_order, $second_order, $third_order)
    {
    	$this->first_order = $first_order;
    	$this->second_order = $second_order;
    	$this->third_order = $third_order;

    	return $this->save();
    }
}
