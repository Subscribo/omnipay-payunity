<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\RequiringAmountPostRequest;

/**
 * Class PostAuthorizeRequest
 *
 * Request for preauthorization
 *
 * @package Omnipay\PayUnity\Message
 */
class PostAuthorizeRequest extends RequiringAmountPostRequest
{
    protected $defaultPaymentType = 'PA';

    protected $addCardReferenceMode = 'full';
}
