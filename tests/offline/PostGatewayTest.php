<?php

namespace Omnipay\PayUnity;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\PostGateway;

class PostGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        $this->gateway = new PostGateway($this->getHttpClient(), $this->getHttpRequest());
    }


    public function testPurchaseMethod()
    {
        $request = $this->gateway->purchase();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostPurchaseRequest', $request);
    }


    public function testRefundMethod()
    {
        $request = $this->gateway->refund();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostRefundRequest', $request);
    }


    public function testVoidMethod()
    {
        $request = $this->gateway->void();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostVoidRequest', $request);
    }
}
