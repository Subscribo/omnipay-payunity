<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;
use Omnipay\PayUnity\AccountRegistrationReference;

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
        return (($this->getProcessingStatusCode() ?: $this->getExtractedProcessingStatusCode())
                    ?: $this->getPostValidationErrorCode());
    }

    public function getMessage()
    {
        return implode(' : ', array_filter([$this->getProcessingReason(), $this->getProcessingReturn()]));
    }

    /**
     * @return string|null
     */
    public function getCardReference()
    {
        $reference = new AccountRegistrationReference($this->getAccountRegistration(), $this->getPaymentCode());

        return $reference->export();
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        return $this->getIdentificationUniqueId();
    }

    /**
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getIdentificationTransactionId();
    }

    /**
     * @return string|null
     */
    public function getAccountRegistration()
    {
        return $this->getTransactionData('account.registration');
    }

    /**
     * @return string|null
     */
    public function getIdentificationUniqueId()
    {
        return $this->getTransactionData('identification.uniqueId');
    }

    /**
     * @return string|null
     */
    public function getIdentificationShortId()
    {
        return $this->getTransactionData('identification.shortId');
    }

    /**
     * @return string|null
     */
    public function getIdentificationTransactionId()
    {
        return $this->getTransactionData('identification.transactionid');
    }

    /**
     * @return string|null
     */
    public function getIdentificationShopperId()
    {
        return $this->getTransactionData('identification.shopperid');
    }

    /**
     * @return string|null
     */
    public function getProcessingReason()
    {
        return $this->getTransactionData('processing.reason');
    }

    /**
     * @return string|null
     */
    public function getProcessingReturn()
    {
        return $this->getTransactionData('processing.return');
    }

    /**
     * @return string|null
     */
    public function getProcessingResult()
    {
        return $this->getTransactionData('processing.result');
    }

    /**
     * @return string|null
     */
    public function getProcessingCode()
    {
        return $this->getTransactionData('processing.code');
    }

    /**
     * @return string|null
     */
    public function getProcessingResultCode()
    {
        return $this->getTransactionData('processing.result.code');
    }

    /**
     * @return string|null
     */
    public function getProcessingReasonCode()
    {
        return $this->getTransactionData('processing.reason.code');
    }

    /**
     * @return string|null
     */
    public function getProcessingStatusCode()
    {
        return $this->getTransactionData('processing.status.code');
    }

    /**
     * @return string|null
     */
    public function getProcessingReturnCode()
    {
        return $this->getTransactionData('processing.return.code');
    }

    /**
     * @return string|null
     */
    public function getPaymentCode()
    {
        return $this->getTransactionData('payment.code');
    }

    /**
     * @return string|null
     */
    public function getPostValidationErrorCode()
    {
        return $this->getTransactionData('post.validation');
    }

    /**
     * @return string|null
     */
    protected function getExtractedProcessingStatusCode()
    {
        $processingCode = $this->getProcessingCode();

        if (empty($processingCode)) {
            return null;
        }

        $parts = explode('.', $processingCode);

        return empty($parts[2]) ? null : $parts[2];
    }

    /**
     * @param $key
     * @return string|null|mixed
     */
    protected function getTransactionData($key)
    {
        $translatedKey = strtr(strtoupper($key), ['.' => '_']);

        if (isset($this->data[$translatedKey])) {
            return $this->data[$translatedKey];
        }

        return null;
    }
}
