<?php

namespace Yijin\AbroadPay;


/**
 * 通用配置参数
 * @property int $channel 支付渠道 1-KBZ支付
 * @property string $charset 请求和返回编码，目前都是UTF-8
 * @property string $tradeNo 商户订单号
 * @property string $refundTradeNo 商户退款订单号
 * @property float $totalAmount 订单总金额
 * @property string $subject 订单标题
 * @property string $authCode B扫C时读取到的条码内容
 * @property string $notifyUrl 支付结果异步通知地址
 *
 * KBZPay参数
 * @property string $kbzAppId 商户的应用id
 * @property string $kbzMerchantCode 商户编码
 * @property string $kbzMerchantKey 商户密钥
 */
class Config
{
    const PAY_BY_KBZ = 1;

    const PAY_SUCCESS = 1;
    const PAYING = 0;
    const PAY_FAIL = -1;

    const REFUND_SUCCESS = 1;
    const REFUNDING = 0;
    const REFUND_FAIL = -1;

    protected $_config = [];

    /**
     * 更多的参数想要传递给支付渠道的，可以放入这个数组，会在请求时合并到请求参数内
     * @var array
     */
    public $optional = [];

    public function __set($name, $value)
    {
        $this->_config[$name] = $value;
    }

    public function __get($name)
    {
        return  $this->_config[$name] ?? null;
    }

    public function __toString()
    {
        return json_encode($this->_config);
    }

    public function __serialize(): array
    {
        return $this->_config;
    }
}
