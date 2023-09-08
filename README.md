# Oyta PHP支付插件

## 安装

- composer require oyta/pospay

## 使用方法

```bash
use Oyta\Pospay\Charge; //引入方法

//设置密钥  密钥使用Rsa2
$pubkey='';
$prekey='';
Charge::setkey($pubkey,$prekey);

//发起支付
$data = [
    'appid' =>  'xxx', //商户APPID
    'secret'    => 'xxxxx', //商户密钥
    'order_no'  => 'xxxxx',   //订单号
    'amount'    => 0.01, //金额 最多两位小数
    'paytype'   => 'ALIPAY', //支付方式 支付宝扫码(ALIPAY)  云闪付扫码(YLPAY)  微信公众号(WXSJAPI)  支付宝服务窗(ALIJSAPI)
    'notify_url'=> 'xxxxxx' //异步通知地址
];
$res = Charge::pay($data);
dump($res);

//支付回调

$data = $_POST['charge'];
$verify = Charge::verify($data);  //数据验签
// ...你的功能代码
$result = [
    'code'=>2000,
    'msg'=>'SUCCESS'
    ];
echo json_encode($result);

```


