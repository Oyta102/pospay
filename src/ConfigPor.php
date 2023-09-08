<?php
/**
 * @Copyright Oyta
 * @Author Oyta
 * @Email oyta@daucn.com
 * @Version 1.0
 */

namespace Oyta\Pospay;

class ConfigPor
{
    public static function get(){
        $file = __DIR__ . '/config.php';
        return $file;
    }
}
