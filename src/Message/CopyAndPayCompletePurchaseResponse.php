<?php namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericCopyAndPayResponse;

/**
 * Class CopyAndPayCompletePurchaseResponse
 *
 * @package Omnipay\PayUnity
 */
class CopyAndPayCompletePurchaseResponse extends GenericCopyAndPayResponse
{
    public function isWaiting()
    {
        return ('WAITING FOR SHOPPER' === $this->getProcessingResult());
    }


    public function getMessage()
    {
        $message = parent::getMessage();
        if ($message) {

            return $message;
        }
        if (isset($this->data['errorMessage'])) {

            return $this->data['errorMessage'];
        }

        return null;
    }
}
