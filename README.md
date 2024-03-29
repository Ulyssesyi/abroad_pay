# 介绍

这个项目是适配公司多个不同海外支付渠道的支付组件，目前支持的支付渠道如下

| 渠道         | 渠道编号 | 进度                             |
|------------|------|--------------------------------|
| Hi Pay     | 1    | （<font color=green>待测试</font>） |
| IPay88 Pay | 2    | （<font color=green>待测试</font>） |
| Gkash Pay  | 3    | （<font color=green>待测试</font>） |
| Stripe Pay | 4    | （<font color=red>开发中</font>）   |

# 更多

可以通过继承Base类后实现更多的支付渠道

# 示例

参看tests文件夹下测试用例的实现

# 请求参数

## 通用参数

| 名称            | 是否必填 | 类型     | 示例                             | 说明                                                            |
|---------------|------|--------|--------------------------------|---------------------------------------------------------------|
| channel       | M    | int    | 1                              | 支付渠道 1-KBZ Pay                                                |
| charset       | C    | string | UTF-8                          | 请求和返回编码，目前都是UTF-8                                             |
| tradeNo       | M    | string | SB202012261548555              | 商户订单号                                                         |
| refundTradeNo | O    | string | SBTK202012261548555            | 商户退款订单号                                                       |
| totalAmount   | O    | float  | 1.23                           | 订单总金额/退款总金额，可多次退款的渠道可能需要同时传入订单金额和退款金额，目前不支持传入不同值，即只能全退        |
| notifyUrl     | O    | string | https://www.abc.com/pay/notify | 支付结果异步通知地址                                                    |
| subject       | O    | string | FPX.Doinb                      | 订单标题                                                          |
| authCode      | O    | string | 12331231321                    | B扫C时读取到的条码内容                                                  |
| optional      | C    | array  | ['a'=>1]                       | 用于更多未添加的参数，当前只写了最小需求的参数，如果有更多需传给第三方的参数，可以通过该数组传入，具体参数请查阅第三方文档 |
| isSandbox     | C    | bool   | false                          | 是否使用了UAT环境                                                    |

## KBZ Pay参数

| 名称              | 是否必填 | 类型     | 示例      | 说明   |
|-----------------|------|--------|---------|------|
| kbzAppId        | M    | string | Sf***** | 应用ID |
| kbzMerchantCode | M    | string | MII**** | 商户编码 |
| kbzMerchantKey  | M    | string | MII**** | 商户私钥 |

## Hi Pay参数

| 名称              | 是否必填 | 类型     | 示例      | 说明   |
|-----------------|------|--------|---------|------|
| hiPayAppId      | M    | string | Sf***** | 应用ID |
| hiPayPrivateKey | M    | string | MII**** | 商户私钥 |
| hiPayPublicKey  | M    | string | MII**** | 应用公钥 |

## IPay88参数

| 名称                    | 是否必填 | 类型     | 示例      | 说明    |
|-----------------------|------|--------|---------|-------|
| iPay88MerchantKey     | M    | string | Sf***** | 商户key |
| iPay88MerchantCode    | M    | string | Sf***** | 商户编码  |
| iPay88MerchantName    | M    | string | Sf***** | 商户名称  |
| iPay88MerchantContact | M    | string | Sf***** | 商户手机号 |
| iPay88MerchantEmail   | M    | string | Sf***** | 商户邮箱  |

## Gkash参数

| 名称               | 是否必填 | 类型     | 示例      | 说明    |
|------------------|------|--------|---------|-------|
| gKashMerchantKey | M    | string | Sf***** | 商户key |
| gKashMerchantCID | M    | string | Sf***** | 商户id  |

## Stripe参数

| 名称                | 是否必填 | 类型     | 示例      | 说明             |
|-------------------|------|--------|---------|----------------|
| stripePublicKey   | M    | string | Sf***** | 应用公钥           |
| stripePrivateKey  | M    | string | Sf***** | 应用私钥           |
| stripeEndKey      | M    | string | Sf***** | 平台公钥           |
| stripeAccount     | M    | string | Sf***** | 商户账号           |
| paymentMethod     | O    | string | xxx     | 支付方式id, 网页支付需要 |
| paymentMethodType | O    | string | card    | 支付方式, 网页支付需要   |
| paymentIntentId   | O    | string | xxx     | 支付id，刷卡支付需要    |

# 返回参数

## 通用参数

| 名称       | 是否必填 | 类型     | 示例             | 说明                                  |
|----------|------|--------|----------------|-------------------------------------|
| result   | M    | bool   | true           | 支付请求结果，true-请求成功，false-请求失败         |
| errMsgNo | C    | mixed  | 1001           | 支付请求失败的失败错误码，用于特定场景的特殊处理            |
| errMsg   | C    | string | 缺失参数xxx        | 支付请求失败的失败原因                         |
| data     | C    | mixed  | https://xxx/xx | 请求成功时，一些额外信息返回，各接口的返回必需参数参看Base类的注释 |

# 名词解释

M-必填，C-可以不填写，O-部分场景下必填
