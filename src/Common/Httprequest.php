<?php
/**
 * @Copyright Oyta
 * @Author Oyta
 * @Email oyta@daucn.com
 * @Version 1.0
 */

namespace Oyta\Pospay\Common;
use Oyta\Pospay\Common\Rsa;
use Oyta\Pospay\ConfigPor;
class Httprequest
{
    public static function uripost($url,$data,$header){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $head = curl_getinfo($ch);
        return $result;
    }

    public static function process($data){
        $keys = ConfigPor::get();
        $key = include($keys);
        $rsa = new Rsa($key['publicKey'],$key['privateKey']); //Rsa
        $sort = json_encode($data);  // 转字符串
        $sign = $rsa->sign($sort);      //私钥构建签名
        $cont = $rsa->pubEncrypt($sort); //平台公钥加密
        $result = array(
            'charge'=>$cont,     //数据密文
            'sign'=>$sign,   //签名
        );
        return $result;
    }

    public static function verify($data){
        $keys = ConfigPor::get();
        $key = include($keys);
        $rsa = new Rsa($key['publicKey'],$key['privateKey']); //Rsa
        $cade = json_decode($data,true);
        if($cade['code'] ==2000){
            $cades = $cade['data'];
            $ery = $rsa->privDecrypt($cades['charge']); //商户使用私钥对数据解密
            $sign = $rsa->verify($ery,$cades['sign']); //商户使用平台公钥对数据验签
            if($sign != 1){
                return '验签失败';
            }
            $result =  json_decode($ery,true); //转换json为数组,最终数据
        }else{
            $result =  $cade;
        }
        return $result;
    }
}
