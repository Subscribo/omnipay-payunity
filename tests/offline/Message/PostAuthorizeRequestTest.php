<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\PostAuthorizeRequest;

class PostAuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $request = new PostAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'PRESENTATION.AMOUNT' => '1.50',
            'PRESENTATION.CURRENCY' => 'GBP',
            'PAYMENT.CODE' => 'CC.PA',
        ];
    }


    public function testGetDataSimple()
    {
        $this->assertSame($this->request, $this->request->setAmount('1.50'));
        $this->assertSame($this->request, $this->request->setCurrency('GBP'));
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
        $this->assertSame($this->request, $this->request->setAmount('1.50'));
        $this->assertSame($this->request, $this->request->setCurrency('GBP'));
        $this->assertSame($this->request, $this->request->setCardReference('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $expected = $this->expectedData;
        $expected['PAYMENT.CODE'] = 'AA.PA';
        $expected['ACCOUNT.REGISTRATION'] = 'test2';
        $this->assertSame($expected, $this->request->getData());
    }


    public function testFill()
    {
        $mockBuilder = $this->getMockBuilder('\\Omnipay\\PayUnity\\Message\\GenericPostResponse')
            ->disableOriginalConstructor();

        $requestA = new PostAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());

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
        $mockResponseA->expects($this->exactly(2))->method('getPresentationAmount')
            ->will($this->returnValue('5.25'));
        $mockResponseA->expects($this->exactly(2))->method('getPresentationCurrency')
            ->will($this->returnValue('EUR'));
        $mockResponseA->expects($this->exactly(2))->method('getPresentationUsage')
            ->will($this->returnValue('Test presentation usage'));

        $this->assertSame($requestA, $requestA->fill($mockResponseA));

        $this->assertSame('a_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $requestA->getCardReference());
        $this->assertSame('5.25', $requestA->getAmount());
        $this->assertSame('EUR', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        /* testing that FILL_MODE_REFERENCES_AND_PRESENTATION is default for this request */

        $requestB = new PostAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertSame($requestB, $requestB->fill($mockResponseA, GenericPostRequest::FILL_MODE_REFERENCES_AND_PRESENTATION));
        $this->assertEquals($requestA, $requestB);
    }

}
