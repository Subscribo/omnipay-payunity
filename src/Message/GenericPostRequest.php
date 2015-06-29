<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractPostRequest;
use Omnipay\PayUnity\AccountRegistrationReference;

/**
 * Class GenericPostRequest
 *
 * @package Omnipay\PayUnity
 *
 * @method \Omnipay\PayUnity\Message\GenericPostResponse send() send()
 * @method \Omnipay\PayUnity\Message\GenericPostResponse sendData() sendData($data)
 */
class GenericPostRequest extends AbstractPostRequest
{
    /**
     * This is to be redefined in particular PostRequest messages
     *
     * @var string
     */
    protected $defaultPaymentType = 'DB';

    protected $defaultPaymentMethod = 'CC';

    protected $addCardReferenceMode = 'paymentMethodOnly'; //other values: 'full', null

    public function getData()
    {
        $result = $this->prepareData();

        $result['PAYMENT.CODE'] = $this->getPaymentCode() ?: $this->assemblePaymentCode();

        $result = $this->addCardReference($result);

        return $result;
    }

    /**
     * @return string|null
     */
    public function getPaymentCode()
    {
        return $this->getParameter('paymentCode');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPaymentCode($value)
    {
        return $this->setParameter('paymentCode', $value);
    }

    /**
     * @return string
     */
    protected function assemblePaymentCode()
    {
        $method = $this->getPaymentMethod() ?: $this->defaultPaymentMethod;
        $type = $this->getPaymentType() ?: $this->defaultPaymentType;

        return $method.'.'.$type;
    }

    /**
     * Changes payment type part in payment code
     *
     * @param string $paymentCode
     * @param string $paymentType
     * @return string
     */
    protected function changePaymentTypeInCode($paymentCode, $paymentType)
    {
        $parts = explode('.', $paymentCode);
        $paymentMethod = reset($parts);

        return $paymentMethod.'.'.$paymentType;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addCardReference(array $data)
    {
        if (empty($this->addCardReferenceMode)) {
            return $data;
        }
        $reference = AccountRegistrationReference::rebuild($this->getCardReference());

        if (empty($reference)) {
            return $data;
        }

        switch($this->addCardReferenceMode) {
            case 'paymentMethodOnly':
                $paymentType = $this->getPaymentType() ?: $this->defaultPaymentType;
                $data['PAYMENT.CODE'] = $this->changePaymentTypeInCode($reference->paymentCode, $paymentType);
                break;
            case 'full':
                $data['ACCOUNT.REGISTRATION'] = $reference->accountRegistration;
                $data['PAYMENT.CODE'] = $reference->paymentCode;
        }

        return $data;
    }

    /**
     * @param array $data
     * @param int $httpStatusCode
     * @return \Omnipay\Common\Message\ResponseInterface|GenericPostResponse
     */
    protected function createResponse(array $data, $httpStatusCode)
    {
        return new GenericPostResponse($this, $data, $httpStatusCode);
    }
}
