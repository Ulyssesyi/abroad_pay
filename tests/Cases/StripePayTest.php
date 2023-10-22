<?php
declare(strict_types=1);

use Yijin\AbroadPay\Config;
use PHPUnit\Framework\TestCase;
use Yijin\AbroadPay\Factory;

class StripePayTest extends TestCase
{
    private $tradeNo;
    protected function setUp(): void
    {
        $this->tradeNo = 'TS' . time();
    }

    public function testQrcodePaySuccess()
    {
        $config = new Config();
        $config->channel = Config::PAY_BY_STRIPE;
        $config->tradeNo = $this->tradeNo;
        $config->totalAmount = 0.01;
        $config->subject = '起飞';
        $config->notifyUrl = 'https://www.baidu.com';

        $config->kbzAppId = getenv('KBZ_APPID');
        $config->kbzMerchantCode = getenv('KBZ_MCH_ID');
        $config->kbzMerchantKey = getenv('KBZ_MCH_KEY');

        $payModel = (new Factory())->getAdapter($config);
        $res = $payModel->qrcodePay();
        $this->assertTrue($res['result'], 'B扫C失败' . json_encode($res,  JSON_UNESCAPED_UNICODE));
        $this->assertArrayHasKey('payUrl', $res['data'], 'C扫B失败' . json_encode($res,  JSON_UNESCAPED_UNICODE));
    }
}
