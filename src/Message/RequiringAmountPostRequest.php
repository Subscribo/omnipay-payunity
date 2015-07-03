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
    protected $defaultFillMode = self::FILL_MODE_REFERENCES_AND_PRESENTATION;

    public function getData()
    {
        $this->validate('amount', 'currency');

        return parent::getData();
    }
}
