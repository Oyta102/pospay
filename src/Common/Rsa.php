<?php
/**
 * @Copyright Oyta
 * @Author Oyta
 * @Email oyta@daucn.com
 * @Version 1.0
 */

namespace Oyta\Pospay\Common;

class Rsa
{
    public $publicKey = '';
    public $privateKey = '';
    private $_privKey;
    private  $encryptBlockSize = 245;//加密切割长度
    private  $decryptBlockSize = 256;//解密切割长度
    /**
     * * private key
     */
    private $_pubKey;

    /**
     * * public key
     */
    private $_keyPath;

    /**
     * * the keys saving path
     */

    /**
     * * the construtor,the param $path is the keys saving path
     */
    function __construct($publicKey = null, $privateKey = null)
    {
        $this->setKey($publicKey, $privateKey);
    }

    /**
     * 设置公钥和私钥
     * @param string $publicKey 公钥
     * @param string $privateKey 私钥
     */
    public function setKey($publicKey = null, $privateKey = null)
    {
        if (!is_null($publicKey))
            $this->publicKey = $publicKey;
        if (!is_null($privateKey))
            $this->privateKey = $privateKey;
    }

    /**
     * * setup the private key
     */
    private function setupPrivKey()
    {
        if (is_resource($this->_privKey))
        {
            return true;
        }
        $pem = chunk_split($this->privateKey, 64, "\n");
        $pem = "-----BEGIN PRIVATE KEY-----\n" . $pem . "-----END PRIVATE KEY-----\n";
        $this->_privKey = openssl_pkey_get_private($pem);
        return true;
    }

    /**
     * * setup the public key
     */
    private function setupPubKey()
    {
        if (is_resource($this->_pubKey))
        {
            return true;
        }
        $pem = chunk_split($this->publicKey, 64, "\n");
        $pem = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
        $this->_pubKey = openssl_pkey_get_public($pem);
        return true;
    }

    /**
     * * encrypt with the private key
     */
    public function privEncrypt($data)
    {
        if (!is_string($data))
        {
            return null;
        }
        $this->setupPrivKey();

        //私钥分段加密
        $result='';
        $data = str_split($data, $this->encryptBlockSize);
        foreach ($data as $block) {
            openssl_private_encrypt($block, $encrypted, $this->_privKey);
            $result .= $encrypted;
        }
        return $result ? base64_encode($result) : null;

        /*$r = openssl_private_encrypt($data, $encrypted, $this->_privKey);
        if ($r)
        {
            return base64_encode($encrypted);
        }
        return null;*/
    }

    /**
     * * decrypt with the private key
     */
    public function privDecrypt($encrypted)
    {
        if (!is_string($encrypted))
        {
            return null;
        }
        $this->setupPrivKey();
        $encrypted = base64_decode($encrypted);

        //分段解密
        $result = '';
        $data = str_split($encrypted, $this->decryptBlockSize);
        foreach ($data as $block) {
            openssl_private_decrypt($block, $decrypted, $this->_privKey);
            $result .= $decrypted;
        }
        return $result ? $result : null;

        /* $r = openssl_private_decrypt($encrypted, $decrypted, $this->_privKey);
         if ($r)
         {
             return $decrypted;
         }
         return null;*/
    }

    /**
     * * encrypt with public key
     */
    public function pubEncrypt($data)
    {
        if (!is_string($data))
        {
            return null;
        }
        $this->setupPubKey();

        //分段加密
        $result='';
        $data = str_split($data, $this->encryptBlockSize);
        foreach ($data as $block) {
            openssl_public_encrypt($block, $encrypted, $this->_pubKey);
            $result .= $encrypted;
        }
        return  $result ? base64_encode($result) : null;

        /*$r = openssl_public_encrypt($data, $encrypted, $this->_pubKey);
        if ($r)
        {
            return base64_encode($encrypted);
        }
        return null;*/
    }

    /**
     * * decrypt with the public key
     */
    public function pubDecrypt($crypted)
    {
        if (!is_string($crypted))
        {
            return null;
        }
        $this->setupPubKey();
        $crypted = base64_decode($crypted);

        $result = '';
        $data = str_split($crypted, $this->decryptBlockSize);
        foreach ($data as $block) {
            openssl_public_decrypt($block, $decrypted, $this->_pubKey);
            $result .= $decrypted;
        }
        return $result ? $result : null;

        /* $r = openssl_public_decrypt($crypted, $decrypted, $this->_pubKey);
         if ($r)
         {
             return $decrypted;
         }
         return null;*/
    }


    /**
     * 构造签名
     * @param string $dataString 被签名数据
     * @return string
     */
    public function sign($dataString)
    {
        $this->setupPrivKey();
        $signature = false;
        openssl_sign($dataString, $signature, $this->_privKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * 验证签名
     * @param string $dataString 被签名数据
     * @param string $signString 已经签名的字符串
     * @return number 1签名正确 0签名错误
     */
    public function verify($dataString, $signString)
    {
        $this->setupPubKey();
        $signature = base64_decode($signString);
        $flg = openssl_verify($dataString, $signature, $this->_pubKey, OPENSSL_ALGO_SHA256);
        return $flg;
    }

    public function __destruct()
    {
        is_resource($this->_privKey) && @openssl_free_key($this->_privKey);
        is_resource($this->_pubKey) && @openssl_free_key($this->_pubKey);
    }
}
