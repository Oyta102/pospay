<?php
/**
 * @Copyright Oyta
 * @Author Oyta
 * @Email oyta@daucn.com
 * @Version 1.0
 */

namespace Oyta\Pospay;

use Oyta\Pospay\Common\Oytapay;
use Oyta\Pospay\Common\Setkey;

class Charge
{
    public static function setkey($publicKey, $privateKey){
        return Setkey::set($publicKey, $privateKey);
    }

    public static function pay($data){
        return Oytapay::dopay($data);
    }

    public static function verify($data){
        return Oytapay::sign($data);
    }
}
