<?php

namespace Cases;

use PHPUnit\Framework\TestCase;
use Yijin\AbroadPay\Config;
use Yijin\AbroadPay\Factory;
use Yijin\AbroadPay\Payment\KbzPay;

class FactoryTest extends TestCase
{
    public function testKbzAdapter() {
        $config = new Config();
        $config->channel = Config::PAY_BY_KBZ;
        $payModel = (new Factory())->getAdapter($config);
        $this->assertInstanceOf(KbzPay::class, $payModel, '工厂实例化Kbz渠道失败');
    }
}
