<?php

namespace Omnipay\PayUnity;

use Omnipay\PayUnity\AbstractGateway;

/**
 * Class PostGateway
 *
 * @package Omnipay\PayUnity
 */
class PostGateway extends AbstractGateway
{
    /**
     * @param array $parameters
     * @return \Omnipay\PayUnity\Message\PostPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\PostPurchaseRequest', $parameters);
    }
}
