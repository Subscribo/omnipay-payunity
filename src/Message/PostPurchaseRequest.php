<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\RequiringAmountPostRequest;

/**
 * Class PostPurchaseRequest
 *
 * @package Omnipay\PayUnity
 */
class PostPurchaseRequest extends RequiringAmountPostRequest
{
    protected $defaultPaymentType = 'DB';

    protected $addCardReferenceMode = 'full';
}
