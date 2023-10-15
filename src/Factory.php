<?php

namespace Yijin\AbroadPay;

use Yijin\AbroadPay\Payment\Base;
use Yijin\AbroadPay\Payment\HiPay;
use Yijin\AbroadPay\Payment\IPay88;
use Yijin\AbroadPay\Payment\KbzPay;

class Factory
{
    /**
     * @param Config $config
     * @return Base
     * @throws \Exception
     */
    function getAdapter(Config $config): Base {
        return match ($config->channel) {
            Config::PAY_BY_KBZ => new KbzPay($config),
            Config::PAY_BY_HIPAY => new HiPay($config),
            Config::PAY_BY_IPAY88 => new IPay88($config),
            default => throw new \Exception('暂时未支持的支付通道'),
        };
    }
}
