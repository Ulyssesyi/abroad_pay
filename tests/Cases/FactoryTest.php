<?php

namespace Cases;

use PHPUnit\Framework\TestCase;
use Yijin\AbroadPay\Config;
use Yijin\AbroadPay\Factory;
use Yijin\AbroadPay\Payment\GKash;
use Yijin\AbroadPay\Payment\HiPay;
use Yijin\AbroadPay\Payment\IPay88;
use Yijin\AbroadPay\Payment\StripePay;

class FactoryTest extends TestCase
{
    public function testGKashAdapter() {
        $config = new Config();
        $config->channel = Config::PAY_BY_GKASH;
        $payModel = (new Factory())->getAdapter($config);
        $this->assertInstanceOf(GKash::class, $payModel, '工厂实例化GKash渠道失败');
    }

    public function testHiPayAdapter() {
        $config = new Config();
        $config->channel = Config::PAY_BY_HIPAY;
        $payModel = (new Factory())->getAdapter($config);
        $this->assertInstanceOf(HiPay::class, $payModel, '工厂实例化HiPay渠道失败');
    }

    public function testIPay88Adapter() {
        $config = new Config();
        $config->channel = Config::PAY_BY_IPAY88;
        $payModel = (new Factory())->getAdapter($config);
        $this->assertInstanceOf(IPay88::class, $payModel, '工厂实例化IPay88渠道失败');
    }

    public function testStripePayAdapter() {
        $config = new Config();
        $config->channel = Config::PAY_BY_STRIPE;
        $payModel = (new Factory())->getAdapter($config);
        $this->assertInstanceOf(StripePay::class, $payModel, '工厂实例化Stripe渠道失败');
    }
}
