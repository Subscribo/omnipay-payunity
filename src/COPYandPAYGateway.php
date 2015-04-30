<?php

namespace Omnipay\PayUnity;

use Omnipay\PayUnity\AbstractGateway;

/**
 * Class COPYandPAYGateway
 *
 * @package Omnipay\PayUnity
 */
class COPYandPAYGateway extends AbstractGateway
{
    /**
     * @param array $parameters
     * @return \Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseRequest', $parameters);
    }
}
