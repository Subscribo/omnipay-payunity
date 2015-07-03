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


    public function testFill()
    {
        $mockBuilder = $this->getMockBuilder('\\Omnipay\\PayUnity\\Message\\GenericPostResponse')
            ->disableOriginalConstructor();

        $requestA = new PostVoidRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->assertNull($requestA->getTransactionReference());
        $this->assertNull($requestA->getCardReference());
        $this->assertNull($requestA->getAmount());
        $this->assertNull($requestA->getCurrency());
        $this->assertNull($requestA->getDescription());

        $mockResponseA = $mockBuilder->getMock();
        $mockResponseA->expects($this->exactly(2))->method('getTransactionReference')
            ->will($this->returnValue('a_transaction_reference'));
        $mockResponseA->expects($this->exactly(2))->method('getCardReference')
            ->will($this->returnValue('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0='));
        $mockResponseA->expects($this->never())->method('getPresentationAmount');
        $mockResponseA->expects($this->never())->method('getPresentationCurrency');
        $mockResponseA->expects($this->never())->method('getPresentationUsage');

        $requestA->fill($mockResponseA);

        $this->assertSame('a_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $requestA->getCardReference());
        $this->assertNull($requestA->getAmount());
        $this->assertNull($requestA->getCurrency());
        $this->assertNull($requestA->getDescription());

        /* testing that FILL_MODE_REFERENCES is default for this request */

        $requestB = new PostVoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $requestB->fill($mockResponseA, GenericPostRequest::FILL_MODE_REFERENCES);
        $this->assertEquals($requestA, $requestB);
    }
}
