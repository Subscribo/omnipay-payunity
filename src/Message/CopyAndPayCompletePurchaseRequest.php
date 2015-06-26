<?php

namespace Omnipay\PayUnity\Message;

use InvalidArgumentException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;
use Subscribo\PsrHttpMessageTools\Factories\RequestFactory;
use Subscribo\PsrHttpMessageTools\Parsers\ResponseParser;

/**
 * Class CopyAndPayCompletePurchaseRequest
 *
 * @package Omnipay\PayUnity
 *
 * @method \Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse send() send()
 */
class CopyAndPayCompletePurchaseRequest extends AbstractRequest
{
    protected $endpointUrlBaseTest = 'https://test.ctpe.net';

    protected $endpointUrlBaseLive = 'https://ctpe.net';

    protected $endpointUrlPath = '/frontend/GetStatus';

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionToken($value)
    {
        return $this->setParameter('transactionToken', $value);
    }

    /**
     * @return string
     */
    public function getTransactionToken()
    {
        return $this->getParameter('transactionToken');
    }

    /**
     * @param CopyAndPayPurchaseResponse $purchaseResponse
     * @return $this
     */
    public function fill(CopyAndPayPurchaseResponse $purchaseResponse)
    {
        return $this->setTransactionToken($purchaseResponse->getTransactionToken());
    }

    /**
     * @param $data
     * @return string
     */
    protected function getEndpointUrl($data)
    {
        $urlBase = $this->getTestMode() ? $this->endpointUrlBaseTest : $this->endpointUrlBaseLive;
        $urlPath = $this->endpointUrlPath;
        $urlSuffix = ';jsessionid='.urlencode($data['transactionToken']);

        return $urlBase.$urlPath.$urlSuffix;
    }

    /**
     * @param $data
     * @return null
     */
    protected function getHttpRequestData($data)
    {
        return null;
    }

    /**
     * @param array $data
     * @param int $httpResponseStatusCode
     * @return CopyAndPayCompletePurchaseResponse
     */
    protected function createResponse(array $data, $httpResponseStatusCode)
    {
        return new CopyAndPayCompletePurchaseResponse($this, $data, $httpResponseStatusCode);
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $transactionToken = $this->getTransactionToken();
        if (empty($transactionToken)) {
            $transactionToken = $this->httpRequest->query->get('token');
        }
        if (empty($transactionToken)) {
            throw new InvalidRequestException('Token has not been provided as parameter, neither found in httpRequest');
        }
        return ['transactionToken' => $transactionToken];
    }

    /**
     * @param array $data
     * @return CopyAndPayCompletePurchaseResponse
     * @throws \InvalidArgumentException
     */
    public function sendData($data)
    {
        if (( ! is_array($data)) or empty($data['transactionToken'])) {
            throw new InvalidArgumentException('Provided data should be an array containing transactionToken key');
        }

        return parent::sendData($data);
    }
}
