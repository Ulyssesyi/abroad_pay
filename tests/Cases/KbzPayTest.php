<?php
declare(strict_types=1);
namespace Cases;

use Yijin\AbroadPay\Config;
use PHPUnit\Framework\TestCase;
use Yijin\AbroadPay\Factory;

class KbzPayTest extends TestCase
{
    private $tradeNo;
    protected function setUp(): void
    {
        $this->tradeNo = 'TS-' . time();
    }

    public function testBarcodePaySuccess()
    {
        $config = new Config();
        $config->channel = Config::PAY_BY_KBZ;
        $config->tradeNo = $this->tradeNo;
        $config->totalAmount = 0.01;
        $config->subject = '起飞';
        $config->authCode = '284129140845289263';

        $config->appId = getenv('KBZ_APPID');
        $config->merchantCode = getenv('KBZ_MCH_ID');
        $config->merchantKey = getenv('KBZ_MCH_KEY');

        $this->assertTrue(!!$config->authCode, '未填入付款码');

        $payModel = (new Factory())->getAdapter($config);
        $res = $payModel->barcodePay();
        $this->assertTrue($res['result'], 'B扫C失败' . json_encode($res,  JSON_UNESCAPED_UNICODE));
        $this->assertSame(Config::PAY_SUCCESS, $res['data']['trade_status'] ?? 0, 'B扫C预期成功未实现'. json_encode($res, JSON_UNESCAPED_UNICODE));
    }
}
