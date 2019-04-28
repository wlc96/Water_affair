<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Request;

class OperatingLog extends Model
{
    use Traits\BaseModel;

    /**
     * 表单验证 用户基础模型认证 
     * saveData 方法会校验此处设置
     * @see http://www.golaravel.com/laravel/docs/5.0/validation/
     * @var array
     */
    protected $validators = [];

    /**
     * 需要被转换成基本类型的属性值。
     * 支持 integer, real, float, double, string, boolean, object 和 array
     * @see http://www.golaravel.com/laravel/docs/5.0/eloquent/#attribute-casting
     *
     * @var array
     */
    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    /**
     * [$types 类型说明]
     * @var [type]
     */
    private static $types = ['create' => '添加', 'update' => '修改', 'delete' => '删除'];

    /**
     * [$caches description]
     * @var array
     */
    private static $caches = [];

    /**
     * [pushCache 存缓存]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $model_name [description]
     * @param    [type]     $model_id   [description]
     * @return   [type]                 [description]
     */
    public static function pushCache($model_name, $model_id)
    {   
        self::$caches[$model_name . '|' . $model_id] = [
            'model_name' => $model_name, 
            'model_id' => $model_id
        ];

        return true;
    }

    /**
     * [deleteCache 清楚全部]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @return   [type]     [description]
     */
    public static function deleteCache()
    {
        foreach (self::$caches as $value) 
        {
            $model_name = $value['model_name'];

            $model_name::_forget($value['model_id']);
        }

        return true;
    }

    /**
     * [getTypeAttribute 获取 type 值]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $value [description]
     * @return   [type]            [description]
     */
    public function getTypeAttribute($value)
    {
        return array_get(self::$types, $value, $value);
    }

    /**
     * [getBeforeDataAttribute 获取变化前数据]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $value [description]
     * @return   [type]            [description]
     */
    public function getBeforeDataAttribute($value)
    {
        if($value)
        {
            $data = [];
            foreach ($value as $k=>$v) 
            {
                if(is_object($v) || is_array($v))
                {
                    $v = json_encode($v);
                }
                $data[$k] = $v;
            }
            $value = $data;
        }

        return $value;
    }

    /**
     * [getAfterDataAttribute 获取变化后数据]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $value [description]
     * @return   [type]            [description]
     */
    public function getAfterDataAttribute($value)
    {
        if($value)
        {
            $data = [];
            foreach ($value as $k=>$v) 
            {
                if(is_object($v) || is_array($v))
                {
                    $v = json_encode($v);
                }
                $data[$k] = $v;
            }
            $value = $data;
        }

        return $value;
    }

    /**
     * [add 操作日志数据入库]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    User       $user   [description]
     * @param    [type]     $obj    [description]
     * @param    [type]     $type   [description]
     * @param    string     $reason [description]
     */
    // public static function add(User $user, $obj, $type, $reason = "")
    // {
    //     list($before_data, $after_data) = $obj->getChangeData($type);

    //     if($obj->modelName() == 'OperatingLog' || $obj->modelName() == 'UserLoginLog') 
    //     {
    //         return ;
    //     }    

    //     $data = [
    //         'user_id' => $user->id,
    //         'company_id' => $user->company_id,
    //         'logable_type' => $obj->modelName(),
    //         'logable_id' => $obj->id,
    //         'type' => $type,
    //         'before_data' => $before_data,
    //         'after_data' => $after_data,
    //         'client_ip' => Request::ip(),
    //         'uri' => Request::getUri(),
    //         'reason' => $reason,
    //     ];
        
    //     return self::saveData($data);
    // }
}
