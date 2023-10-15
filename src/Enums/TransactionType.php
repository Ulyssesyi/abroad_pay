<?php

namespace Yijin\AbroadPay\Enums;

enum TransactionType
{
    case BARCODE_PAY;
    case QRCODE_PAY;
    case WEB_PAY;
    case QUERY;
    case REFUND;
    case REFUND_QUERY;
}
