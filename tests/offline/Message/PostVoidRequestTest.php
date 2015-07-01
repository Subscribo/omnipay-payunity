<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\PostVoidRequest;

class PostVoidRequestTest extends TestCase
{
    public function setUp()
    {
        $request = new PostVoidRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'PAYMENT.CODE' => 'CC.RV',
        ];
    }


    public function testGetDataSimple()
    {
        $this->assertSame($this->expectedData, $this->request->getData());
    }


    public function testAddCardReference()
    {
        $this->assertSame($this->request, $this->request->setCardReference('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $expected = $this->expectedData;
        $expected['PAYMENT.CODE'] = 'AA.RV';
        $this->assertSame($expected, $this->request->getData());
    }
}
