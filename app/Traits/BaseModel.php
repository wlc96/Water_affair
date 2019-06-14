<?php 

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Auth, Request, App\OperatingLog, App\User;

trait BaseModel {

    /**
    * 默认使用时间戳戳功能
    *
    * @var bool
    */
    public $timestamps = true;
  
    /**
     * 是否开启对象缓存
     * @var boolean
     */
    protected static $cache = true;

    /**
     * 当前缓存版本
     * @var integer
     */
    protected static $cache_version = 0;

    /**
    * 用户当前对象缓存
    *
    * @var array
    */
    public static $object_cached = array();

    /**
     * Get uniqe key
     *
     * @param  integer $id
     * @param  string  $array
     */
    private static function key_cache($id)
    {
        return 'slc/'.snake_case(str_plural(get_called_class())).'/'.$id.'/'.static::$cache_version;
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static|null
     */
    public static function find($id)
    {
        return self::_find($id);
    }

    /**
     * find 的影子方法
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $id      [description]
     * @param  [type]     $onWrite 强制从主库（写库）查询
     * @param  array      $columns [description]
     * @return [type]              [description]
     */
    protected static function _find($id, $onWrite = false, $query = null)
    {
        $key = self::key_cache($id);

        // 去掉当前进程缓存，修复常驻脚本不更新缓存问题 by huxf 2017-11-03
        // if ( ! $result = array_get(static::$object_cached, $key))
        // {
            $result = Cache::tags(get_called_class())->rememberForever($key, function() use($id, $onWrite, $query)
            {
                if(is_null($query))
                {
                    $query = $onWrite ? static::onWriteConnection() : static::query();
                }

                return $query->find($id, array('*'));
            });

        //     array_set(static::$object_cached, $key, $result);
        // }

        if($result && is_object($result))
        {
            return clone $result;
        }

        return $result;
    }

    /**
     * 清除当前对象所有缓存
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    public static function flush()
    {
        Cache::tags(get_called_class())->flush();

        static::$object_cached = [];
    }

    /**
     * 清楚当前记录缓存
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    public function forget()
    {
        $this->_forget($this->id);
    }

    /**
     * 清除单条缓存
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $id [description]
     * @return [type]         [description]
     */
    public static function _forget($id)
    {
        $key = self::key_cache($id);

        if(Cache::tags(get_called_class())->has($key))
        {
            Cache::tags(get_called_class())->forget($key);
        }

        array_forget(static::$object_cached, $key);
    }

    /**
     * Fire the given event for the model.
     *
     * @param  string  $event
     * @param  bool    $halt
     * @return mixed
     */
    protected function fireModelEvent($event, $halt = true)
    {
        parent::fireModelEvent($event, $halt);

        $this->_fireModelEvent($event, $halt);
    }

    /**
     * [_fireModelEvent description]
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    protected function _fireModelEvent($event, $halt = true)
    {
        if($event == "created")
        {
            $this->afterCreate();
        }

        if($event == "updated")
        {
            $this->afterUpdate();
        }

        if($event == "deleted")
        {
            $this->afterDelete();
        }

        // if cache enabled
        if (static::$cache === true)
        {
            // 去掉 saved 如果调用save方法但没有执行update操作时可不用清楚缓存，进一步提交缓存利用率 add by huxf 2016-08-17
            if (in_array($event, ['updated', /*'saved',*/ 'deleted']))
            {
                self::_forget($this->id);
            }

            // 记录每个操作的缓存
            // if (in_array($event, ['created', 'updated', 'deleted']))
            // {
            //     OperatingLog::pushCache(self::modelName(), $this->id);
            // }
        }
    }

    /**
     * 重写 increment 添加清除缓存
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $column [description]
     * @param  integer    $amount [description]
     * @param  array      $extra  [description]
     * @return [type]             [description]
     */
    protected function increment($column, $amount = 1, array $extra = [])
    {
        $result = parent::increment($column, $amount, $extra);

        $this->forget();

        return $result;
    }

    /**
     * 重写 decrement 添加清除缓存
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $column [description]
     * @param  integer    $amount [description]
     * @param  array      $extra  [description]
     * @return [type]             [description]
     */
    protected function decrement($column, $amount = 1, array $extra = [])
    {
        $result = parent::decrement($column, $amount, $extra);

        $this->forget();

        return $result;
    }

    /**
     * [get_method_cache_key description]
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $method [description]
     * @param  [type]     $args   [description]
     * @return [type]             [description]
     */
    protected function get_method_cache_key($method, $args)
    {
        $args = array_dot($args);

        ksort($args);
        
        return "Method_".self::modelName().":".$this->id."_".$method."_".md5(json_encode($args));
    }

    /**
     * 缓存某个方法的结果
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $method   [description]
     * @param  [type]     $args     [description]
     * @param  [type]     $callback [description]
     * @param  [type]     $minutes  [description]
     * @return [type]               [description]
     */
    protected function cacheMethodResult($method, $args, $callback, $minutes = null)
    {   
        // 最大缓存30天
        $minutes = $minutes ? $minutes : (30 * 86400);

        $cache_key = $this->get_method_cache_key($method, $args);

        return Cache::tags(get_called_class())->remember($cache_key, $minutes, $callback);
    }

    /**
     * 通过二位数组组合查询条件
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $query [description]
     * @return [type]            [description]
     */
    public function scopeMultiWhere($query)
    {
        $table = $this->getTable();

        $numargs = func_num_args();
        $arg_lists = func_get_args();

        if($numargs < 2 || !is_array($arg_lists[1]) || count($arg_lists[1]) == 0)
        {
            return $query;
        }

        $wheres = [];

        if(is_array($arg_lists[1]))
        {
            $wheres = $arg_lists[1];
        }
        else
        {
            for($i = 1 ; $i < $numargs ; $i++)
            {
                $wheres[] = $arg_lists[$i];
            }
        }

        foreach($wheres as $k => $where)
        {
            if(!is_array($where))
            {
                $where = [$k, $where];
            }

            if(strpos($where[0],'.') === false){
                $where[0] = $table.'.'.$where[0];
            }

            if($where[1] == "in" && count($where) == 3)
            {
                $query->whereIn($where[0], $where[2]);
                unset($wheres[$k]);
            }
            else
            {
                $query = call_user_func_array([$query , 'where'], $where);
            }
        }

        return $query;
    }

    /**
     * 复制当前对象
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * 克隆当前查询句柄
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $query [description]
     * @return [type]            [description]
     */
    public function scopeClone($query)
    {
        return clone $query;
    }

    
     /**
      * Please don't touch my code.
      * @Author   wulichuan
      * @DateTime 2019-04-12
      * @param    array      $select [description]
      * @return   [type]             [description]
      *  eg: return $user->only('id', 'name', 'name("aaaa","11") nickname', 'avatar("120x130") avatar_tb', 'avatar() avatar_url');
      */
    public function only($select=[])
    {
        if (func_num_args() > 1 || !is_array($select)){
            $select = func_get_args();
        }

        if(!$select) {
            return $this;
        }

        $attributes = $original = [];

        if(in_array('*',$select)){
            foreach($this->attributes as $k => $v){
                $attributes[$k] = $original[$k] = $v;
            }
        }

        foreach($select as $attr){
            if($attr === '*') { 
                continue;
            }

            $key = $attr = trim($attr);

            if($args = explode(' ', $attr)){
                if(count($args) > 1)
                    list($attr, $key) = $args;
            }

            $method = $attr;
            $attr = preg_replace('/\(.*\)/', '', $attr);
            $key = preg_replace('/\(.*\)/', '', $key);

            if(array_key_exists($attr, $this->attributes)){
                $attributes[$key] = $original[$key] = $this->attributes[$attr];
            }

            if( method_exists($this, $attr)){
                $attributes[$key] = $original[$key] = eval('return $this->'.$method.';');
            }
        }

        $object = $this->copy();

        $object->attributes = $attributes;
        $object->original = $original;

        return $object;
    }

    /**
     * 排除指定的对象成员
     * @param  [type] $select [description]
     * @return [type]         [description]
     * @author wulichuan
     */
    public function except($select=[])
    {
        if (func_num_args() > 1 || !is_array($select)){
            $select = func_get_args();
        }

        $select = array_diff(array_keys($this->attributes), $select);

        return $this->only($select);
    }

    /**
     * 重新加载model
     *
     * @author wulichuan
     * @version [version]
     * @date    2019-04-10
     * @return  [type]     [description]
     */
    public function reloadModel()
    {
        $this->setRawAttributes($this->find($this->id)->getAttributes(), true);
    }

    /**
     * 通过数组添加对象
     * @author wulichuan
     * @date   2019-04-10
     * @param  array      $attributes [description]
     * @return [type]                 [description]
     */
    public static function saveData($attributes=array())
    {
        $model = new static;

        foreach ($attributes as $key => $value) 
        {
            $model->$key = $value;
        }

        // 验证表单
        $model->validate();

        $model->save();

        return static::_find($model->id, true);
    }

    /**
     * 更新数据
     * @author wulichuan
     * @date   2019-04-10
     * @param  array      $attributes [description]
     * @return [type]                 [description]
     */
    public function updateData($attributes=array())
    {
        foreach ($attributes as $key => $value) 
        {
            $this->$key = $value;
        }

        // 验证表单
        $this->validate();

        $this->save();

        return $this;
    }

    /**
     * 表单认证
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    public function validate()
    {
        // 如果设置了 validators 对象，这进行认证
        if($validators = $this->validators)
        {
            foreach ($validators as $key => $value) 
            {
                // 替换当前ID
                $validators[$key] = str_replace('{id}', $this->id, $value);
            }

            $validator = \Validator::make($this->attributes, $validators);

            if($validator->fails())
            {
                return failure($validator->messages()->first());
            }
        }

        return true;
    }

    /**
     * 对象名
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    public static function modelName()
    {
        return get_called_class();
    }

    /**
     * 获取当前模型的初始类型
     * @author wulichuan
     * @date   2019-04-10
     * @return [type]     [description]
     */
    public static function modelType($type = 0)
    {
        if($type >= 1000)
        {
            failure('type 不能大于等于1000');
        }

        return \App\ModelType::getBase(get_called_class()) + $type;
    }

    /**
     * 返回业务对象
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $key [description]
     * @return [type]          [description]
     */
    public function abled($key=null)
    {
        $able_id = $key.'_id';
        $able_type = $key.'_type';

        if(array_key_exists($able_id, $this->attributes) && array_key_exists($able_type, $this->attributes))
        {
            $obj = new $this->$able_type();
            return $obj->find($this->$able_id);
        }

        $cur_model_name = self::modelName();
        throw new \OutOfBoundsException("Properties of {$able_id} and {$able_type} was not found in  {$cur_model_name}", self::modelType());
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->___get($key);
    }

    /**
     * 重写get
     * @author wulichuan
     * @date   2019-04-10
     * @param  [type]     $key [description]
     * @return [type]          [description]
     */
    public function ___get($key)
    {
        // 自动转 model_id
        $field_id = snake_case($key).'_id';

        if(!array_key_exists($key, $this->attributes) && array_key_exists($field_id, $this->attributes))
        {
            $model = 'App\\'.studly_case($key);
            return $model::find($this->$field_id);
        }

        return parent::__get($key);
    }

    /**
     * [afterUpdate 更新之后操作]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @return   [type]     [description]
     */
    protected function afterUpdate()
    {
        $this->addOperatingLog(__FUNCTION__);
    }

    /**
     * [afterDelete 删除之后操作]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @return   [type]     [description]
     */
    protected function afterDelete()
    {
        $this->addOperatingLog(__FUNCTION__);
    }

    /**
     * [afterCreate 创建之后操作]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @return   [type]     [description]
     */
    protected function afterCreate()
    {
        $this->addOperatingLog(__FUNCTION__);
    }

    /**
     * [addOperatingLog 管理员操作数据日志入口]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    [type]     $method [description]
     */
    protected function addOperatingLog($method)
    {   
        if(!$user = Auth::user())
        {
            return ;
        }

        if(!$manager = Auth::user()->manager())
        {
            return ;
        }

        // 必须是后台操作
        if(!User::$loginFormManager)
        {
            return ;
        }

        $type = str_replace("after", "", strtolower($method));
        OperatingLog::add($manager, $this, $type, Request::input("delete_reason",""));
    }

    /**
     * [getChangeData 获取改变的数据]
     * This is a cool function
     * @author wulichuan
     * @DateTime 2019-04-10
     * @param    string     $type [description]
     * @return   [type]           [description]
     */
    public function getChangeData($type="")
    {
        $original = $attributes = [];

        if($type == 'delete' || $type == 'deleted')
        {
            $original = $this->original;
        }
        else if($type=='create' || $type == 'inserted')
        {
            $attributes = $this->attributes;
        }
        else
        {
            foreach ($this->attributes as $k => $v) 
            {

                if(!is_null($v) && !isset($this->original[$k]))
                {
                    $attributes[$k] = $v;
                }
                else if(array_get($this->original, $k, '') !== $this->attributes[$k])
                {
                    $attributes[$k] = $v;
                    $original[$k] = array_get($this->original, $k, '');
                }
            }
        } 

        $original = array_except($original,['updated_at', 'deleted_at']);
        $attributes = array_except($attributes,['updated_at', 'deleted_at']);

        return [$original, $attributes];
    }

}
