<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericPostRequest;

/**
 * Class RequiringAmountPostRequest
 * A parent class for Post Gateway requests, requiring amount and currency parameters
 *
 * @package Omnipay\PayUnity\Message
 */
class RequiringAmountPostRequest extends GenericPostRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency');

        return parent::getData();
    }
}
