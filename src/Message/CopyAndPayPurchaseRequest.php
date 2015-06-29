<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractPostRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

/**
 * Class CopyAndPayPurchaseRequest
 *
 * @package Omnipay\PayUnity
 *
 * @method \Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse send() send()
 */
class CopyAndPayPurchaseRequest extends AbstractPostRequest
{
    protected $endpointUrlBaseTest = 'https://test.ctpe.net';

    protected $endpointUrlBaseLive = 'https://ctpe.net';

    protected $endpointUrlPath = '/frontend/GenerateToken';

    protected $defaultPaymentType = 'DB';

    /**
     * @return null|string|array
     */
    public function getBrands()
    {
        return $this->getParameter('brands');
    }

    /**
     * @param string|array $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setBrands($value)
    {
        return $this->setParameter('brands', $value);
    }

    /**
     * @param array $data
     * @param int $httpResponseStatusCode
     * @return CopyAndPayPurchaseResponse
     */
    protected function createResponse(array $data, $httpResponseStatusCode)
    {
        return new CopyAndPayPurchaseResponse($this, $data, $httpResponseStatusCode);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('currency', 'amount');

        $result = $this->prepareData();

        $result['PAYMENT.TYPE'] = $this->getPaymentType() ?: $this->choosePaymentType();

        return $result;
    }

    /**
     * @return string
     */
    protected function choosePaymentType()
    {
        $paymentType = $this->defaultPaymentType;
        if ($this->getRegistrationMode()) {
            $paymentType = 'RG.'.$paymentType;
        }
        return $paymentType;
    }
}
