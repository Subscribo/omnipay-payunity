<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\RequiringAmountPostRequest;

/**
 * Class PostRefundRequest
 *
 * Request for partial or full refund
 *
 * @package Omnipay\PayUnity
 */
class PostRefundRequest extends RequiringAmountPostRequest
{
    protected $defaultPaymentType = 'RF';
}
