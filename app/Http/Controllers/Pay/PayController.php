<?php
namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class PayController extends Controller{
    public function payList(){
        return view('Pay.paylist');
    }

    public function payTest(){
        //appid
        $appid = "2016092300576201";
        //网关
        $ali_gateway = 'https://openapi.alipaydev.com/gateway.do';

        //请求参数
        $biz_cont = [
            'subject' => '测试订单'.mt_rand(11111,99999).time(),
            'out_trade_no' => '1810_'.mt_rand(11111,99999).time(),
            'total_amount'=>mt_rand(1,100) / 100,
            'product_code' => 'QUICK_WAP_WAY'
        ];

        //公共参数
        $data = [
            'app_id'=>$appid,
            'method'=>'alipay.trade.wap.pay',
            'charset'=>'utf-8',
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'biz_content' => json_encode($biz_cont)
        ];

        //排序参数
        ksort($data);

        //拼接带签名字符串
        $str0 = "";
        foreach ($data as $k=>$v) {
            $str0 .= $k . '=' . $v . '&';
        }
        $str = rtrim($str0,'&');


        //私钥签名
        $private_key = openssl_get_privatekey("file://".public_path('keys/rsa_private.pem'));
        openssl_sign($str,$signature,$private_key,OPENSSL_ALGO_SHA256);
        $data['sign'] = base64_encode($signature);

        //urlencode
        $param_str = '?';
        foreach($data as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }
        $param = rtrim($param_str,'&');
        $url = $ali_gateway . $param;

        //发送请求
        header("Location:".$url);
    }
}