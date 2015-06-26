<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractPostRequest;

class PostPurchaseRequest extends AbstractPostRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency');

        $result = $this->prepareData();

        $result['PAYMENT.CODE'] = 'CC.DB';

        $result = $this->addCardReference($result);

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addCardReference(array $data)
    {
        $cardReference = $this->getCardReference();

        if ($cardReference) {
            $decoded = base64_decode($cardReference, true);
            $parsed = json_decode($decoded, true, 2, JSON_BIGINT_AS_STRING);
            $data['ACCOUNT.REGISTRATION'] = $parsed['registration'];
            $data['PAYMENT.CODE'] = $parsed['code'];
        }

        return $data;
    }
}
