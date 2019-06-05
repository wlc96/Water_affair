<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WaterCity;
use App\WaterCompany;
use App\UserGroup;
use App\Equipment;
use App\Recharge;
use App\UserEquipmentBind;
use function EasyWeChat\Kernel\Support\generate_sign;
use DB;

class PayController extends Controller
{

    /**
     * 支付接口
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-06-03
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function pay(Request $request)
    {
        if (!$type = $request->input('type')) 
        {
            return failure('请选择支付类型');
        }

        if ($type == 1 ) 
        {
            $data = $this->payWeChat();
        }
        elseif ($type == 2) 
        {
            $data = $this->payALi();
        }
        else
        {
            return failure('类型选择错误');
        }

        return success(['data' => $data]);
    }

    /**
     * 微信支付
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-06-03
     * @return   [type]     [description]
     */
    private function payWeChat()
    {
        $str = '26351824092183721983701293801924720640218730219730101928301724546';

        $code = '0330ZSor0EVZ9g1bItqr0G91pr00ZSoj';
        $num = 'YMZH'.substr(str_shuffle($str),3,10);
        $mini = \EasyWeChat::miniProgram();

        // $result = $mini->auth->session($code);
        $openid = 'ogYH-4ywq56jfVxxx4wOMSBumf9Q';
        // return $result['openid'];
        $payment = \EasyWeChat::payment(); // 微信支付
        // return $payment->order;
        $result = $payment->order->unify([
            'body'         => 'water',
            'out_trade_no' => $num,
            'trade_type'   => 'JSAPI',  // 必须为JSAPI
            'openid'       => $openid, // 这里的openid为付款人的openid
            'total_fee'    => 1, // 总价
        ]);

        // return $result;
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId'     => 'wx092b3da2b80333d3',
                'timeStamp' => time(),
                'nonceStr'  => $result['nonce_str'],
                'package'   => 'prepay_id=' . $result['prepay_id'],
                'signType'  => 'MD5',
            ];

            // config('wechat.payment.default.key')为商户的key
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));
            $params['timeStamp'] = (string)$params['timeStamp'];
            return $params;
        } else {
            return $result;
        }
    }

    /**
     * 支付宝支付
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-06-03
     * @return   [type]     [description]
     */
    private function payALi()
    {

    }

	/**
	 * 获取水务公司城市列表
	 * Please don't touch my code.
	 * @Author   wulichuan
	 * @DateTime 2019-05-29
	 * @param    Request    $request [description]
	 * @return   [type]              [description]
	 */
    public function waterCityList(Request $request)
    {
    	$data = WaterCity::list();

    	return success(['data' => $data]);
    }

    /**
     * todo 户名校验
     * 用户绑定水表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-29
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function bindEquipment(Request $request)
    {
    	$user = $request->user;
    	if (!$water_company_id = $request->input('water_company_id')) 
    	{
    		return failure('请输入缴费单位id');
    	}

    	if (!$water_company = WaterCompany::where('id', $water_company_id)->first()) 
    	{
    		return failure('该水务公司不存在');
    	}

    	if (!$group_id = $request->input('group_id')) 
    	{
    		return failure('请选择组id');
    	}

    	if (!$group = UserGroup::where('id', $group_id)->first()) 
    	{
    		return failure('该分组不存在');
    	}

    	if (!$equipment_id = $request->input('equipment_id')) 
    	{
    		return failure('请输入水表号');
    	}

    	if (!$equipment = Equipment::where('id', $equipment_id)->first()) 
    	{
    		return failure('该水表不存在');
    	}

    	if (!$hu_name = $request->input('hu_name')) 
    	{
    		return failure('请输入户名');
    	}

    	return DB::transaction(function() use ($user, $water_company, $equipment, $group, $hu_name)
    	{
    		$data = UserEquipmentBind::add($user, $water_company, $equipment, $group, $hu_name);
    		
    		return success(['data' => $data]);
    	});
    }

    /**
     * 水表分组列表
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-05-30
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function groupList(Request $request)
    {
    	$datas = UserGroup::all();

    	return success(['data' => $datas]);
    }

    /**
     * 微信回调
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-06-05
     * @return   [type]     [description]
     */
    public function wechatNotify()
    {
        $response = $this->wxpay->handlePaidNotify(function ($message, $fail) {
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Recharge::where('order_no', $message['out_trade_no'])->first();
            //订单不存在或者订单已支付
            if ($order) {
                return true;
            }
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                if (array_get($message, 'result_code') === 'SUCCESS') { // 用户是否支付成功
                    $order->status = 1; // 更新支付时间为当前时间
                    $order->transaction_id = $message['transaction_id'];  //微信交易号
                } elseif (array_get($message, 'result_code') === 'FAIL') {  // 用户支付失败
                    $order->status = 0; //支付状态 失败
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            // $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        return response()->json(['code' => 1, 'msg' => '订单支付成功!', 'data' => $response]);
    }
}
