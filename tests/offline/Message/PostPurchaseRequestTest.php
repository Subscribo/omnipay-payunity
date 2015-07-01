<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\PostPurchaseRequest;

class PostPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $request = new PostPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->initializationData = [
            'testMode' => true,
            'securitySender' => 'some security sender',
            'transactionChannel' => 'some transaction channel',
            'userLogin' => 'some username',
            'userPwd' => 'some password',
        ];
        $request->initialize($this->initializationData);
        $this->request = $request;
        $this->expectedData = [
            'REQUEST.VERSION' => '1.0',
            'SECURITY.SENDER' => 'some security sender',
            'TRANSACTION.CHANNEL' => 'some transaction channel',
            'TRANSACTION.MODE' => 'INTEGRATOR_TEST',
            'TRANSACTION.RESPONSE' => 'SYNC',
            'USER.LOGIN' => 'some username',
            'USER.PWD' => 'some password',
            'PRESENTATION.AMOUNT' => '0.50',
            'PRESENTATION.CURRENCY' => 'EUR',
            'PAYMENT.CODE' => 'CC.DB',
        ];
    }


    public function testGetDataSimple()
    {
        $this->assertSame($this->request, $this->request->setAmount('0.50'));
        $this->assertSame($this->request, $this->request->setCurrency('EUR'));
        $this->assertSame($this->expectedData, $this->request->getData());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The amount parameter is required
     */
    public function testParameterValidation()
    {
        $this->request->getData();
    }


    public function testAddCardReference()
    {
        $this->assertSame($this->request, $this->request->setAmount('0.50'));
        $this->assertSame($this->request, $this->request->setCurrency('EUR'));
        $this->assertSame($this->request, $this->request->setCardReference('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $expected = $this->expectedData;
        $expected['PAYMENT.CODE'] = 'AA.BB';
        $expected['ACCOUNT.REGISTRATION'] = 'test2';
        $this->assertSame($expected, $this->request->getData());
    }
}
