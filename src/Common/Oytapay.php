<?php
/**
 * @Copyright Oyta
 * @Author Oyta
 * @Email oyta@daucn.com
 * @Version 1.0
 */

namespace Oyta\Pospay\Common;
use Oyta\Pospay\Common\Httprequest;

class Oytapay
{
    public static function dopay($data){
        $pays = [
            'appid'     =>  $data['appid'], //商户APPID
            'timestamp' =>  time(),   //发起时间
            'secret'    =>  $data['secret'], //商户密钥
            'order_no'  =>  $data['order_no'],   //订单号
            'amount'    =>  $data['amount'], //金额 最多两位小数
            'paytype'   =>  $data['paytype'], //支付方式 支付宝扫码(ALIPAY)  云闪付扫码(YLPAY)  微信公众号(WXSJAPI)  支付宝服务窗(ALIJSAPI)
            'notify_url'=>  $data['notify_url'] //异步通知地址
        ];
        $process = Httprequest::process($pays);
        $url = 'https://card.daucn.com/api/pay/charge';
        $posts = Httprequest::uripost($url,$process,null);
        $res = Httprequest::verify($posts);
        return $res;
    }

    public static function sign($data){
        return Httprequest::verify($data);
    }
}
