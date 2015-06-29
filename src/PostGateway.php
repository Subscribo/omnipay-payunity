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

    /**
     * @param array $parameters
     * @return \Omnipay\PayUnity\Message\PostVoidRequest
     */
    public function void(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\PostVoidRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\PayUnity\Message\PostRefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\PostRefundRequest', $parameters);
    }
}
