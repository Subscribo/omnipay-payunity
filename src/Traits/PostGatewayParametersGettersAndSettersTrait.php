<?php

namespace Omnipay\PayUnity\Traits;

/**
 * Trait PostGatewayParametersGettersAndSettersTrait
 *
 * @package Omnipay\PayUnity
 */
trait PostGatewayParametersGettersAndSettersTrait
{
    /**
     * @return string
     */
    public function getSecurityHashSecret()
    {
        return $this->getParameter('securityHashSecret');
    }

    /**
     * @param string $value
     * @return string
     */
    public function setSecurityHashSecret($value)
    {
        return $this->setParameter('securityHashSecret', $value);
    }
}
