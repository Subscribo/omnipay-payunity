<?php namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Traits\DefaultGatewayParametersGettersAndSettersTrait;

/**
 * Class CopyAndPayAbstractRequest
 *
 * @package Omnipay\PayUnity
 */
abstract class CopyAndPayAbstractRequest extends AbstractRequest
{
    use DefaultGatewayParametersGettersAndSettersTrait;

    /**
     * @return string
     */
    public function getCardReference()
    {
        return $this->getIdentificationReferenceId();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCardReference($value)
    {
        return $this->setIdentificationReferenceId($value);
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentType($value)
    {
        return $this->setParameter('paymentType', $value);
    }

    /**
     * @return string
     */
    public function getIdentificationReferenceId()
    {
        return $this->getParameter('identificationReferenceId');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setIdentificationReferenceId($value)
    {
        return $this->setParameter('identificationReferenceId', $value);
    }


    /**
     * @return string
     */
    public function getPresentationUsage()
    {
        return $this->getParameter('presentationUsage');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPresentationUsage($value)
    {
        return $this->setParameter('presentationUsage', $value);
    }

    /**
     * @return string
     */
    public function getPaymentMemo()
    {
        return $this->getParameter('paymentMemo');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentMemo($value)
    {
        return $this->setParameter('paymentMemo', $value);
    }
}
