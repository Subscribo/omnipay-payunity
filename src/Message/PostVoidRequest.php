<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericPostRequest;


/**
 * Class PostVoidRequest
 *
 * Request for transaction reversal
 *
 * @package Omnipay\PayUnity
 */
class PostVoidRequest extends GenericPostRequest
{
    protected $defaultPaymentType = 'RV';
}
