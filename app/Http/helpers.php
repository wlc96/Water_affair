<?php
    
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 自定义异常
 */
class CustomException extends \Exception {

    /**
     * 更多的数据
     * @var array
     */
    public $data = [];
}



/**
 * [paginate_walk 分页数据拼装回调]
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @param    LengthAwarePaginator $paginate [description]
 * @param    [type]               $callback [description]
 * @return   [type]                         [description]
 */
function paginate_walk(LengthAwarePaginator $paginate, $callback)
{
    $paginate->setCollection($paginate->getCollection()->map($callback));

    return $paginate;
}

/**
 * 过滤
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @param    LengthAwarePaginator $paginate [description]
 * @param    [type]               $callback [description]
 * @return   [type]                         [description]
 */
function paginate_filter(LengthAwarePaginator $paginate, $callback)
{
    $paginate->setCollection($paginate->getCollection()->filter($callback)->values());

    return $paginate;
}

/**
 * 成功返回数据
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @return   [type]     [description]
 */
function success()
{
    $res = [
        'result' => true,
    ];

    $data = [];

    foreach (func_get_args() as $key => $value) 
    {
        if(is_string($value))
        {
            $res['message'] = $value;
        }else if(is_array($value))
        {
            $data = $value;
        }
        else if(is_object($value))
        {
            $data = $value->toArray();
        }
    }

    $data = array_merge($res, $data);

    if(Request::input('format') == 'jsonp')
    {
        return response()->jsonp(Request::input('callback'), $data);                
    }

    return response()->json($data);
}

/**
 * 失败抛出异常
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @param    [type]     $message [description]
 * @param    integer    $code    [description]
 * @param    array      $data    [description]
 * @return   [type]              [description]
 */
function failure($message, $code=1, $data=[])
{
    if(is_null($code)){
        $e = new \CustomException($message);
    }
    else
    {
        $e = new \CustomException($message, $code);
    }

    $e->data = $data;

    throw $e;
}


/**
 * [verifyPhone 邮箱验证]
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @param    [type]     $email [description]
 * @return   [type]            [description]
 */
function verifyEmail($email)
{
    $pattern = "/^([a-zA-Z0-9]+[_|\_|\.|\-]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.|\-]?)*[a-zA-Z0-9]+\.[a-zA-Z]+$/i";
    return preg_match($pattern, $email);
}

/**
 * [verifyPhone 手机验证]
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @param    [type]     $phone [description]
 * @return   [type]            [description]
 */
function verifyPhone($phone)
{
    return preg_match('/^1\d{10}$/', $phone);
}




/**
 * 遍历文件夹
 * Please don't touch my code.
 * @Author   wulichuan
 * @DateTime 2019-04-09
 * @param    [type]     $dir   [description]
 * @param    [type]     &$data [description]
 * @return   [type]            [description]
 */
function listDir($dir, &$data)
{
    if(is_dir($dir))
    {
        if ($dh = opendir($dir))
        {
            while (($file = readdir($dh)) !== false)
            {
                if((is_dir($dir."/".$file)) && $file!="." && $file!="..")
                {
                    listDir($dir."/".$file, $data);
                }
                else
                {
                    if($file!="." && $file!="..")
                    {
                        $data[] = $dir."/".$file;
                    }
                }
            }
            closedir($dh);
        }
    }
}
