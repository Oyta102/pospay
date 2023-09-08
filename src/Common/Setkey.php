<?php
/**
 * @Copyright Oyta
 * @Author Oyta
 * @Email oyta@daucn.com
 * @Version 1.0
 */

namespace Oyta\Pospay\Common;
use Oyta\Pospay\ConfigPor;

class Setkey
{
    public static function set($publicKey, $privateKey){
        $file = ConfigPor::get();
        $config = self::filepath($file);
        $arr = ['publicKey' => $publicKey,'privateKey' =>$privateKey];
        file_put_contents($file, "<?php\n\nreturn " . var_export($arr, true) . ';');
        return $config;
    }

    public static function filepath($file){
        return require($file);
    }
}
