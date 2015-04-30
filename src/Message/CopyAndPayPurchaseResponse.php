<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractResponse;
use Omnipay\PayUnity\Widget\CopyAndPayWidget;

/**
 * Class CopyAndPayPurchaseResponse
 *
 * @package Omnipay\PayUnity
 */
class CopyAndPayPurchaseResponse extends AbstractResponse
{
    /** @var  \Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest */
    protected $request;
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isTransactionToken()
    {
        return ( ! empty($this->data['transaction']['token']));
    }

    /**
     * @return bool
     */
    public function haveWidget()
    {
        return $this->isTransactionToken();
    }

    /**
     * @return null|string
     */
    public function getTransactionToken()
    {
        return empty($this->data['transaction']['token']) ? null : $this->data['transaction']['token'];
    }

    /**
     * @param array $parameters
     * @return CopyAndPayWidget|null
     */
    public function getWidget(array $parameters = [])
    {
        if (( ! $this->haveWidget())) {
            return null;
        }
        $defaultParameters = [
            'transactionToken' => $this->getTransactionToken(),
        ];
        if ($this->request) {
            $defaultParameters['testMode'] = $this->request->getTestMode();
            $defaultParameters['returnUrl'] = $this->request->getReturnUrl();
            $defaultParameters['brands'] = $this->request->getBrands();
        }
        $parameters = array_replace($defaultParameters, $parameters);
        $widget = new CopyAndPayWidget($parameters);

        return $widget;
    }
}
