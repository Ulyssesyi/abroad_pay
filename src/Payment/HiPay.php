<?php

namespace Yijin\AbroadPay\Payment;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yijin\AbroadPay\Config;
use Yijin\AbroadPay\Response;

class HiPay extends Base
{
    /**
     * 商户的扫码支付地址;
     */
    const ORDER_PAY_URL = '/payment/v1/charge/create';
    /**
     * 商户的支付查询地址;
     */
    const ORDER_QUERY_URL = '/payment/v1/charge/query';

    /**
     * 商户的退款地址;
     */
    const REFUND_URL = '/payment/v1/refund/create';

    /**
     * 商户的退款查询地址;
     */
    const REFUND_QUERY_URL = '/payment/v1/refund/query';
    const DOMAIN = 'https://api.pay.hwipg.com';
    const DOMAIN_UAT = 'https://api.pay-uat.hwipg.com';

    const SING_TYPE = 'SHA256withRSA';
    const API_METHOD_MAP = [
        self::ORDER_PAY_URL => 'ft.charge.create',
        self::ORDER_QUERY_URL => 'ft.charge.query',
        self::REFUND_URL => 'ft.refund.create',
        self::REFUND_QUERY_URL => 'ft.refund.query',
    ];

    use Response;
    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    function barcodePay()
    {
        $params = [
            'notify_url' => $this->config->notifyUrl,
            'merch_order_id' => $this->config->tradeNo,
            'channel' => 'mm_kbzpay_micropay',
            'amount' => $this->config->totalAmount,
            'currency' => 'MMK',
            'goods_subject' => $this->config->subject ?? 'Merchant Order',
            'goods_body' => '',
            'time_expire' => 15 * 60,
            'channel_extra' => [
                'auth_code' => $this->config->authCode,
                'trans_type' => 'OnlinePaymentISV'
            ],
            'meta_data' => [],
        ];
        try {
            $res = $this->execRequest($params, self::ORDER_PAY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }

        if ($this->isSuccess($res)) {
            $data = $res['biz_content'] ?? [];
            if ($data['paid']) {
                $trade_status = Config::PAY_SUCCESS;
            } else {
                $trade_status = Config::PAYING;
            }
            return $this->success(array_merge($data, compact('trade_status')));
        } else {
            return $this->error($res['error_msg'] ?? '系统异常', $res['error_code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function qrcodePay()
    {
        $params = [
            'notify_url' => $this->config->notifyUrl,
            'merch_order_id' => $this->config->tradeNo,
            'channel' => 'mm_kbzpay_paybyqrcode',
            'amount' => $this->config->totalAmount,
            'currency' => 'MMK',
            'goods_subject' => $this->config->subject ?? 'Merchant Order',
            'goods_body' => '',
            'time_expire' => 15 * 60,
            'channel_extra' => [],
            'meta_data' => [],
        ];
        try {
            $res = $this->execRequest($params, self::ORDER_PAY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }

        if ($this->isSuccess($res)) {
            $data = $res['biz_content'] ?? [];
            $payUrl = $data['credential']['mm_kbzpay_paybyqrcode']['qrCode'] ?? '';
            return $this->success(array_merge($data, compact('payUrl')));
        } else {
            return $this->error($res['error_msg'] ?? '系统异常', $res['error_code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function webPay()
    {
        $params = [
            'notify_url' => $this->config->notifyUrl,
            'merch_order_id' => $this->config->tradeNo,
            'channel' => 'mm_kbzpay_pwa',
            'amount' => $this->config->totalAmount,
            'currency' => 'MMK',
            'goods_subject' => $this->config->subject ?? 'Merchant Order',
            'goods_body' => '',
            'time_expire' => 15 * 60,
            'channel_extra' => [],
            'meta_data' => [],
        ];
        try {
            $res = $this->execRequest($params, self::ORDER_PAY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }

        if ($this->isSuccess($res)) {
            $data = $res['biz_content'] ?? [];
            $payUrl = ( $this->config->isSandbox ? 'https://static.kbzpay.com/pgw/uat/pwa/#/?' : 'https://wap.kbzpay.com/pgw/pwa/#/') . ($data['credential']['mm_kbzpay_pwa']['rawRequest'] ?? '');
            return $this->success(array_merge($data, compact('payUrl')));
        } else {
            return $this->error($res['error_msg'] ?? '系统异常', $res['error_code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function query()
    {
        $params = [
            'merch_order_id' => $this->config->tradeNo,
        ];
        try {
            $res = $this->execRequest($params, self::ORDER_QUERY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }

        if ($this->isSuccess($res)) {
            $data = $res['biz_content'] ?? [];
            if ($data['paid']) {
                $trade_status = Config::PAY_SUCCESS;
            } else {
                $trade_status = Config::PAYING;
            }
            return $this->success(array_merge($data, compact('trade_status')));
        } else {
            return $this->error($res['error_msg'] ?? '系统异常', $res['error_code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function refund()
    {
        // TODO: Implement refund() method.
    }

    /**
     * @inheritDoc
     */
    function refundQuery()
    {
        // TODO: Implement refundQuery() method.
    }

    /**
     * @inheritDoc
     */
    function notify($data)
    {
        // TODO: Implement notify() method.
    }

    /**
     * @inheritDoc
     */
    function notifySuccess()
    {
        // TODO: Implement notifySuccess() method.
    }

    /**
     * @inheritDoc
     */
    function sign($data): string
    {
        list($params, $time, $requestId) = $data;
        $str = implode('|', [json_encode($params, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE), $this->config->hipayAppId, self::SING_TYPE, $requestId, $time]);
        $priKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->config->hiPayPrivateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        openssl_sign($str, $signed, $priKey, 'sha256');
        return base64_encode($signed);
    }

    /**
     * @inheritDoc
     */
    function verifySign(array $data): bool
    {
        // TODO: Implement verifySign() method.
    }

    /**
     * @throws GuzzleException
     */
    private function execRequest($params, $url) {
        $time = intval(microtime(true) * 1000);
        $requestId = uniqid();
        $content = [
            'method' => self::API_METHOD_MAP[$url],
            "nonce_str" => md5($time),
            'version' => '1.0',
            "biz_content" => array_merge($params, $this->config->optional),
        ];
        $sign = $this->sign([$content, $time, $requestId]);

        $client = new Client([
            'base_uri' => $this->config->isSandbox ? self::DOMAIN_UAT : self::DOMAIN,
            'timeout' => 10,
            'http_errors'=> false
        ]);
        $response = $client->post($url, [
            'body' => json_encode($content, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE),
            'headers' => [
                'Content-Type' => 'application/json',
                'X-ft-AppId' => $this->config->hipayAppId,
                'X-ft-SignType' => self::SING_TYPE,
                'X-ft-Sign' => $sign,
                'X-ft-RequestId' => $requestId,
                'X-ft-Timestamp' => $time,
            ],
        ]);
        $responseData = $response->getBody()->getContents();
        return json_decode($responseData, true);
    }

    private function isSuccess($data): bool
    {
        return isset($data['result']) && $data['result'] === 'SUCCESS';
    }
}
