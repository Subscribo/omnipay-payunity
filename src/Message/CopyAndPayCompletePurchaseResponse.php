<?php namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;

/**
 * Class CopyAndPayCompletePurchaseResponse
 *
 * @package Omnipay\PayUnity
 */
class CopyAndPayCompletePurchaseResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        if (empty($this->data['transaction']['processing']['result'])) {
            return false;
        }
        if ('ACK' === $this->data['transaction']['processing']['result']) {
            return true;
        }
        return false;
    }

    public function isWaiting()
    {
        if (empty($this->data['transaction']['processing']['result'])) {
            return false;
        }
        if ('WAITING FOR SHOPPER' === $this->data['transaction']['processing']['result']) {
            return true;
        }
        return false;
    }

    public function getTransactionReference()
    {
        return $this->getIdentificationUniqueId();
    }

    public function getTransactionId()
    {
        return $this->getIdentificationTransactionId();
    }

    public function getMessage()
    {
        if (isset($this->data['transaction']['processing']['return']['message'])) {
            return $this->data['transaction']['processing']['return']['message'];
        }
        if (isset($this->data['errorMessage'])) {
            return $this->data['errorMessage'];
        }
        return null;
    }


    public function getCode()
    {
        if (isset($this->data['transaction']['processing']['return']['code'])) {
            return $this->data['transaction']['processing']['return']['code'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getCardReference()
    {
        if (empty($this->data['transaction']['account']['registration'])) {
            return null;
        }
        return $this->data['transaction']['account']['registration'];
    }

    public function getIdentificationUniqueId()
    {
        if (isset($this->data['transaction']['identification']['uniqueId'])) {
            return $this->data['transaction']['identification']['uniqueId'];
        }
        return null;
    }

    public function getIdentificationShortId()
    {
        if (isset($this->data['transaction']['identification']['shortId'])) {
            return $this->data['transaction']['identification']['shortId'];
        }
        return null;
    }

    public function getIdentificationTransactionId()
    {
        if (isset($this->data['transaction']['identification']['transactionid'])) {
            return $this->data['transaction']['identification']['transactionid'];
        }
        return null;
    }

    public function getIdentificationShopperId()
    {
        if (isset($this->data['transaction']['identification']['shopperid'])) {
            return $this->data['transaction']['identification']['shopperid'];
        }
        return null;
    }
}
