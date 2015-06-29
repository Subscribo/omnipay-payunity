<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericPostRequest;


/**
 * Class PostRefundRequest
 *
 * Request for partial or full refund
 *
 * @package Omnipay\PayUnity
 */
class PostRefundRequest extends GenericPostRequest
{
    protected $defaultPaymentType = 'RF';
}
