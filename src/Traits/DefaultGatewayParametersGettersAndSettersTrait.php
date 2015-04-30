<?php namespace Omnipay\PayUnity\Traits;

/**
 * Trait DefaultGatewayParametersGettersAndSettersTrait
 *
 * @package Omnipay\PayUnity
 */
trait DefaultGatewayParametersGettersAndSettersTrait
{
    /**
     * @return string
     */
    public function getSecuritySender()
    {
        return $this->getParameter('securitySender');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSecuritySender($value)
    {
        return $this->setParameter('securitySender', $value);
    }

    /**
     * @return string
     */
    public function getTransactionChannel()
    {
        return $this->getParameter('transactionChannel');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionChannel($value)
    {
        return $this->setParameter('transactionChannel', $value);
    }

    /**
     * @return string
     */
    public function getTransactionMode()
    {
        return $this->getParameter('transactionMode');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionMode($value)
    {
        return $this->setParameter('transactionMode', $value);
    }

    /**
     * @return string
     */
    public function getUserLogin()
    {
        return $this->getParameter('userLogin');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUserLogin($value)
    {
        return $this->setParameter('userLogin', $value);
    }

    /**
     * @return string
     */
    public function getUserPwd()
    {
        return $this->getParameter('userPwd');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUserPwd($value)
    {
        return $this->setParameter('userPwd', $value);
    }

    /**
     * @return bool
     */
    public function getRegistrationMode()
    {
        return $this->getParameter('registrationMode');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setRegistrationMode($value)
    {
        return $this->setParameter('registrationMode', $value);
    }

    /**
     * @return string|int
     */
    public function getIdentificationShopperId()
    {
        return $this->getParameter('identificationShopperId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setIdentificationShopperId($value)
    {
        return $this->setParameter('identificationShopperId', $value);
    }

    /**
     * @return string|int
     */
    public function getIdentificationInvoiceId()
    {
        return $this->getParameter('identificationInvoiceId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setIdentificationInvoiceId($value)
    {
        return $this->setParameter('identificationInvoiceId', $value);
    }

    /**
     * @return string|int
     */
    public function getIdentificationBulkId()
    {
        return $this->getParameter('identificationBulkId');
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function setIdentificationBulkId($value)
    {
        return $this->setParameter('identificationBulkId', $value);
    }
}
