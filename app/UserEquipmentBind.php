<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEquipmentBind extends Model
{
    use Traits\BaseModel, SoftDeletes;
}
