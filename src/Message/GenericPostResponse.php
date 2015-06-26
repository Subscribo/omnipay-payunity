<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;

/**
 * Class GenericPostResponse
 *
 * @package Omnipay\PayUnity
 */
class GenericPostResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return (($this->getHttpResponseStatusCode() === 200) and ($this->getProcessingResult() === 'ACK'));
    }


    public function getCode()
    {
        return $this->getProcessingStatusCode() ?: $this->getPostValidationErrorCode();
    }


    public function getProcessingResult()
    {
        return $this->getTransactionData('processing.result');
    }


    public function getProcessingStatusCode()
    {
        return $this->getTransactionData('processing.status.code');
    }


    public function getPostValidationErrorCode()
    {
        return $this->getTransactionData('post.validation');
    }


    protected function getTransactionData($key)
    {
        $translatedKey = strtr(strtoupper($key), ['.' => '_']);

        if (isset($this->data[$translatedKey])) {
            return $this->data[$translatedKey];
        }

        return null;
    }
}
