<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Company;

class Directory extends Model
{
    use Traits\BaseModel, SoftDeletes;
}
