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
    protected $liveEndpointUrl = 'https://ctpe.net/frontend/GetStatus';

    protected $testEndpointUrl = 'https://test.ctpe.net/frontend/GetStatus';


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
     * @return string
     */
    protected function getEndpointUrl()
    {
        return $this->getTestMode() ? $this->testEndpointUrl : $this->liveEndpointUrl;
    }

    /**
     * @param array $data
     * @return CopyAndPayCompletePurchaseResponse
     */
    protected function createResponse($data)
    {
        return new CopyAndPayCompletePurchaseResponse($this, $data);
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
        $uriSuffix = ';jsessionid='.urlencode($data['transactionToken']);
        $url = $this->getEndpointUrl().$uriSuffix;
        $request = RequestFactory::make($url);
        $response = $this->sendHttpMessage($request, true);
        $responseData = ResponseParser::extractDataFromResponse($response);
        $this->response = $this->createResponse($responseData);
        return $this->response;
    }
}
