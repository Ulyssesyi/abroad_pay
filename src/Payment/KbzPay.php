<?php

namespace Yijin\AbroadPay\Payment;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yijin\AbroadPay\Config;
use Yijin\AbroadPay\Response;

class KbzPay extends Base
{
    // B扫C
    const BARCODE_PAY_URL = 'order/reverseScan';
    // C扫B
    const QRCODE_PAY_URL = 'precreate';
    // js网页支付URL
    const JS_PAY_URL = 'order/jsapiScan';
    // 订单支付结果查询
    const ORDER_QUERY_URL = 'queryorder';
    // 退款接口
    const REFUND_URL = 'refund';
    // 退款查询接口
    const REFUND_QUERY_URL = 'queryrefund';
    // 接口域名
    const DOMAIN = 'http://api.kbzpay.com/payment/gateway/uat/';
//    const DOMAIN = 'https://api.kbzpay.com/payment/gateway/';

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

    }

    /**
     * @inheritDoc
     */
    function qrcodePay()
    {
        $params = [
            "appid" => $this->config->kbzAppId,
            "merch_code" => $this->config->kbzMerchantCode,
            "merch_order_id" => $this->config->tradeNo,
            "trade_type" => "PAY_BY_QRCODE",
            "title" => $this->config->subject ?? '前台结算',
            "total_amount" => $this->config->totalAmount,
            "trans_currency" => "MMK",
        ];
        try {
            $res = $this->execRequest($params, self::QRCODE_PAY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        if ($this->isSuccess($res)) {
            $res['payUrl'] = $res['qrCode'] ?? '';
            if ($res['payUrl']) {
                return $this->success($res);
            }
            return $this->error('参数异常', -1);
        } else {
            return $this->error($res['msg'] ?? '系统异常', $res['code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function webPay()
    {

    }

    /**
     * @inheritDoc
     */
    function query()
    {
        $params = [
            "appid" => $this->config->kbzAppId,
            "merch_code" => $this->config->kbzMerchantCode,
            "merch_order_id" => $this->config->tradeNo,
        ];
        try {
            $res = $this->execRequest($params, self::ORDER_QUERY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        if ($this->isSuccess($res)) {
            $status = $res['trade_status'] ?? '';
            switch ($status) {
                case 'PAY_SUCCESS':
                    $res['trade_status'] = 1;
                    break;
                case 'PAY_FAILED':
                case 'ORDER_EXPIRED':
                case 'ORDER_CLOSED':
                    $res['trade_status'] = -1;
                    break;
                case 'WAIT_PAY':
                case 'PAYING':
                    $res['trade_status'] = 0;
                    break;
                default:
                    return $this->error('参数异常', -1);
            }
            return $this->success($res);
        } else {
            return $this->error($res['msg'] ?? '系统异常', $res['code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function refund()
    {
        $params = [
            "appid" => $this->config->kbzAppId,
            "merch_code" => $this->config->kbzMerchantCode,
            "merch_order_id" => $this->config->tradeNo,
            "refund_request_no" => $this->config->refundTradeNo
        ];
        try {
            $res = $this->execRequest($params, self::REFUND_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        if ($this->isSuccess($res)) {
            $status = $res['refund_status'] ?? '';
            switch ($status) {
                case 'REFUND_SUCCESS':
                    $res['refund_status'] = 1;
                    break;
                case 'REFUNDING':
                    $res['refund_status'] = 0;
                    break;
                case 'REFUND_FAILED':
                    $res['refund_status'] = -1;
                    break;
                default:
                    return $this->error('参数异常', -1);
            }
            return $this->success($res);
        } else {
            return $this->error($res['msg'] ?? '系统异常', $res['code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function refundQuery()
    {
        $params = [
            "appid" => $this->config->kbzAppId,
            "merch_code" => $this->config->kbzMerchantCode,
            "merch_order_id" => $this->config->tradeNo,
            "refund_request_no" => $this->config->refundTradeNo,
        ];
        try {
            $res = $this->execRequest($params, self::REFUND_QUERY_URL);
        } catch (GuzzleException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        if ($this->isSuccess($res)) {
            $status = $res['refund_finished'] ?? '';
            switch ($status) {
                case 'Y':
                    $res['refund_status'] = 1;
                    break;
                case 'N':
                    $res['refund_status'] = 0;
                    break;
                default:
                    return $this->error('参数异常', -1);
            }
            return $this->success($res);
        } else {
            return $this->error($res['msg'] ?? '系统异常', $res['code'] ?? -1);
        }
    }

    /**
     * @inheritDoc
     */
    function notify($data)
    {
        if (!$this->verifySign($data)) {
            return $this->error('验签失败', -1);
        }
        $status = $data['trade_status'] ?? '';
        if ($status === 'PAY_SUCCESS') {
            $merchantTradeNo = $data['merch_order_id'] ?? '';
            return $this->success(array_merge($data, compact('merchantTradeNo')));
        } else {
            return $this->error("回调错误", -1);
        }
    }

    /**
     * @inheritDoc
     */
    function notifySuccess()
    {
        return 'success';
    }

    /**
     * @inheritDoc
     */
    function sign($data): string
    {
        $bizContent = $data['biz_content'] ?? [];
        $data = array_merge($data, $bizContent);
        ksort($data);
        $str = [];
        foreach ($data as $key => $val) {
            if ($key === 'sign' || $key === 'sign_type' || $key === 'biz_content'|| $key === 'refund_info' || empty($val)) {
                continue;
            }
            $str[] = "{$key}={$val}";
        }
        $str[] = "key={$this->config->kbzMerchantKey}";
        return strtoupper(hash('sha256', implode('&', $str)));
    }

    /**
     * @inheritDoc
     */
    function verifySign(array $data): bool
    {
        return $data['sign'] === $this->sign($data);
    }

    /**
     * @throws GuzzleException
     */
    private function execRequest($params, $url) {
        $commonParams = [
            "timestamp" => time(),
            "notify_url" => $this->config->notifyUrl,
            "method" => "kbz.payment.{$url}",
            "nonce_str" => md5(time()),
            "sign_type" => "SHA256",
            "version" => "1.0",
            "biz_content" => array_merge($params, $this->config->optional),
        ];
        $commonParams['sign'] = $this->sign($commonParams);
        echo json_encode($commonParams);

        $client = new Client([
            'base_uri' => self::DOMAIN,
        ]);
        $response = $client->post($url, [
            'json' => ['Request' => $commonParams],
        ]);
        $responseData = $response->getBody()->getContents();
        $data = json_decode($responseData, true);
        return $data['Response'] ?? [];
    }

    private function isSuccess($data): bool
    {
        return isset($data['result']) && $data['result'] === 'SUCCESS';
    }
}
