<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\GenericPostResponse;

class GenericCopyAndPayResponse extends GenericPostResponse
{
    /**
     * @param string $key
     * @return mixed|null|string
     */
    protected function getTransactionData($key)
    {
        if (empty($this->data['transaction'])) {

            return null;
        }
        $value  = $this->data['transaction'];
        $keyParts = explode('.', $key);
        foreach ($keyParts as $keyPart) {
            if (isset($value[$keyPart])) {
                $value = $value[$keyPart];
            } else {

                return null;
            }
        }

        return $value;
    }

    /**
     * @return string|null
     */
    public function getTransactionResponse()
    {
        return $this->getTransactionData('response');
    }

    /**
     * @return string|null
     */
    public function getProcessingReturn()
    {
        return $this->getTransactionData('processing.return.message');
    }

    /**
     * @return string|null
     */
    public function getProcessingReason()
    {
        return $this->getTransactionData('processing.reason.message');
    }
}
