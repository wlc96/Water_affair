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
use AopClient;
use Carbon\Carbon;
use AlipaySystemOauthTokenRequest;
use AlipayTradeCreateRequest;

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
            $data = $this->payWeChat($request);
        }
        elseif ($type == 2) 
        {
            $data = $this->payALi($request);
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
    private function payWeChat(Request $request)
    {
        $recharge = $this->infoChek($request, 1);
        if (!$code = $request->input('code')) 
        {
            return failure('请提交code');
        }

        $mini = \EasyWeChat::miniProgram();     
        $result = $mini->auth->session($code);   

        $openid = $result['openid'];

        $payment = \EasyWeChat::payment(); // 微信支付方法

        $result = $payment->order->unify([
            'body'         => '个人水费充值', //商品名
            'out_trade_no' => $recharge->number, //订单号
            'trade_type'   => 'JSAPI',  // 必须为JSAPI
            'openid'       => $openid, // 这里的openid为付款人的openid
            'total_fee'    => ($recharge->sum)*100, // 总价
        ]);

        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId'     => 'wx092b3da2b80333d3', //小程序id
                'timeStamp' => time(), //时间
                'nonceStr'  => $result['nonce_str'], //随机字符串
                'package'   => 'prepay_id=' . $result['prepay_id'], //包
                'signType'  => 'MD5',  //加密类型
            ];

            // config('wechat.payment.default.key')为商户的key
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));//加密字符串
            $params['timeStamp'] = (string)$params['timeStamp'];//时间戳
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
    private function payALi(Request $request)
    {
        $recharge = $this->infoChek($request, 2);
        $code = $request->input('code');
        // $money = $request->input('money');
        // $str = '26351824092183721983701293801924720640218730219730101928301724546';

        // $this_time = Carbon::now()->toDateTimeString();
        // $num = 'YMZH'.$this_time.substr(str_shuffle($str),3,10);
        require_once "../app/libs/alipay/aop/AopClient.php";
        require_once "../app/libs/alipay/aop/request/AlipaySystemOauthTokenRequest.php";
        require_once "../app/libs/alipay/aop/request/AlipayTradeCreateRequest.php";
        require_once "../app/libs/alipay/aop/SignData.php";
        // $recharge = $this->infoChek($request, 2);
        $aop = new AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2019052765384414';
        $aop->rsaPrivateKey = config('app.aliskey');
        $aop->alipayrsaPublicKey= config('app.aligkey');
        // $aop->notify_url = 'https://w.ym-zh.cn/wecaht/alicallback';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='utf-8';
        $aop->format='json';
        $request = new AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($code);
        $result = $aop->execute($request);
        // return $result;
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultId = $result->$responseNode->user_id;
        // return $resultId;
        $request = new AlipayTradeCreateRequest();
        $request->setBizContent("{" .
        "\"out_trade_no\":\"".$recharge->number."\",".
        "\"total_amount\":".$recharge->sum.",".
        "\"subject\":\"个人水费充值\",".
        "\"body\":\"个人水费充值\",".
        "\"buyer_id\":\"".$resultId."\"".
        "}");
        $result = $aop->execute ($request); 
        return $result;
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
        echo "成功";
        } else {
        echo "失败";
        }

        // https://www.merchant.com/receive_notify.htm?notify_type=trade_status_sync&notify_id=91722adff935e8cfa58b3aabf4dead6ibe&notify_time=2017-02-16 21:46:15&sign_type=RSA2&sign=WcO+t3D8Kg71dTlKwN7r9PzUOXeaBJwp8/FOuSxcuSkXsoVYxBpsAidprySCjHCjmaglNcjoKJQLJ28/Asl93joTW39FX6i07lXhnbPknezAlwmvPdnQuI01HZsZF9V1i6ggZjBiAd5lG8bZtTxZOJ87ub2i9GuJ3Nr/NUc9VeY=&refund_preset_paytool_list=[{"amount":"1.00","assert_type_code":"HEMA"}]&charge_amount=8.88&charge_flags=bluesea_1&settlement_id=2018101610032004620239146945

    }

    /**
     * 检查订单提交信息
     * Please don't touch my code.
     * @Author   wulichuan
     * @DateTime 2019-06-06
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    private function infoChek(Request $request, $type)
    {
        $user = $request->user;
        if (!$equipment_id = $request->input('equipment_id')) 
        {
            return failure('请选择水表号');
        }

        if (!$equipment = Equipment::where('id', $equipment_id)->first()) 
        {
            return failure('该水表不存在');
        }

        if (!$money = $request->input('money')) 
        {
            return failure('请输入金额');
        }

        $recharge = Recharge::add($user, $equipment, $money, $type);

        return $recharge;
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
        $this->wxpay = app('easywechat.payment');
        $response = $this->wxpay->handlePaidNotify(function ($message, $fail) {
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Recharge::where('number', $message['out_trade_no'])->first();
            //订单不存在或者订单已支付
            if ($order->status == 1) {
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
            $equipment = $order->equipment;
            $equipment->surplus_money += $order->sum;
            $equipment->save();
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        return response()->json(['code' => 1, 'msg' => '订单支付成功!', 'data' => $response]);
    }

    public function aliNotify(Request $request)
    {
        Storage::put('test.txt', $request);

        return true;
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

}
