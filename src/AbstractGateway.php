<?php

namespace Omnipay\PayUnity;

use Subscribo\Omnipay\Shared\AbstractGateway as Base;
use Omnipay\PayUnity\Traits\DefaultGatewayParametersGettersAndSettersTrait;

/**
 * Abstract class AbstractGateway
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractGateway extends Base
{
    use DefaultGatewayParametersGettersAndSettersTrait;

    public function getName()
    {
        return 'PayUnity';
    }

    public function getDefaultParameters()
    {
        return [
            'securitySender' => '',
            'transactionChannel' => '',
            'transactionMode' => '',
            'userLogin' => '',
            'userPwd' => '',
            'identificationShopperId' => '',
            'identificationInvoiceId' => '',
            'identificationBulkId' => '',
            'testMode' => false,
            'registrationMode' => false,
        ];
    }
}
