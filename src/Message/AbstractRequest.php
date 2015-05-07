<?php namespace Omnipay\PayUnity\Message;

use Subscribo\Omnipay\Shared\Message\AbstractRequest as Base;
use Subscribo\PsrHttpMessageTools\Factories\RequestFactory;
use Subscribo\PsrHttpMessageTools\Parsers\ResponseParser;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\PayUnity\Traits\DefaultGatewayParametersGettersAndSettersTrait;

/**
 * Abstract Class AbstractRequest
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractRequest extends Base
{
    use DefaultGatewayParametersGettersAndSettersTrait;

    abstract protected function getEndpointUrl();

    /**
     * @param $data
     * @return ResponseInterface
     */
    abstract protected function createResponse($data);

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


    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $url = $this->getEndpointUrl();
        $request = RequestFactory::make($url, $data);
        $response = $this->sendHttpMessage($request, true);
        $responseData = ResponseParser::extractDataFromResponse($response);
        $this->response = $this->createResponse($responseData);
        return $this->response;
    }
}
