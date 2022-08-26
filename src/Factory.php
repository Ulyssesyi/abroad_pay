<?php

namespace Yijin\AbroadPay;

use Yijin\AbroadPay\Payment\Base;
use Yijin\AbroadPay\Payment\KbzPay;

class Factory
{
    /**
     * @param Config $config
     * @return Base
     * @throws \Exception
     */
    function getAdapter(Config $config): Base {
        switch ($config->channel) {
            case Config::PAY_BY_KBZ:
                return new KbzPay($config);
            default:
                throw new \Exception('暂时未支持的支付通道');
        }
    }
}
