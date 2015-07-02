<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\RequiringAmountPostRequest;

/**
 * Class PostCaptureRequest
 *
 * @package Omnipay\PayUnity
 */
class PostCaptureRequest extends RequiringAmountPostRequest
{
    protected $defaultPaymentType = 'CP';
}
