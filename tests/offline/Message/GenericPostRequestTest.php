<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\GenericPostRequest;

class GenericPostRequestTest extends TestCase
{
    public function setUp()
    {
        $request = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
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
            'PAYMENT.CODE' => 'CC.DB',
        ];
    }


    public function testGettersAndSetters()
    {
        $request = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertSame($request, $request->setTestMode(true));
        $this->assertNull($request->getPaymentCode());
        $this->assertSame($request, $request->setPaymentCode('AA.BB'));
        $this->assertSame('AA.BB', $request->getPaymentCode());
        $this->assertSame($request, $request->setPaymentCode(null));
        $this->assertNull($request->getPaymentCode());
    }


    public function testFill()
    {
        $request1 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request1->getTransactionReference());
        $this->assertNull($request1->getCardReference());
        $mockBuilder = $this->getMockBuilder('\\Omnipay\\PayUnity\\Message\\GenericPostResponse')
                            ->disableOriginalConstructor();
        $mockResponse1 = $mockBuilder->getMock();
        $mockResponse1->expects($this->once())->method('getTransactionReference')
            ->will($this->returnValue('some_transaction_reference'));
        $mockResponse1->expects($this->once())->method('getCardReference')
            ->will($this->returnValue('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $request1->fill($mockResponse1);
        $this->assertSame('some_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request1->getCardReference());

        $mockResponse2 = $mockBuilder->getMock();
        $mockResponse2->expects($this->any())->method('getTransactionReference')
            ->will($this->returnValue('another_transaction_reference'));
        $mockResponse2->expects($this->any())->method('getCardReference')
            ->will($this->returnValue(''));
        $request2 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request2->fill($mockResponse2, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('another_transaction_reference', $request2->getTransactionReference());
        $this->assertNull($request2->getCardReference());

        $request1->fill($mockResponse2);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request1->getCardReference());

        $mockResponse3 = $mockBuilder->getMock();
        $mockResponse3->expects($this->once())->method('getTransactionReference')
            ->will($this->returnValue('some_transaction_reference'));
        $mockResponse3->expects($this->never())->method('getCardReference');
        $request3 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request3->fill($mockResponse3, GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE);
        $this->assertSame('some_transaction_reference', $request3->getTransactionReference());
        $this->assertNull($request3->getCardReference());

        $mockResponse4 = $mockBuilder->getMock();
        $mockResponse4->expects($this->never())->method('getTransactionReference');
        $mockResponse4->expects($this->once())->method('getCardReference')
            ->will($this->returnValue('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $request4 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request4->fill($mockResponse4, GenericPostRequest::FILL_MODE_CARD_REFERENCE);
        $this->assertNull($request4->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request4->getCardReference());

        $mockResponse5 = $mockBuilder->getMock();
        $mockResponse5->expects($this->once())->method('getTransactionReference')
            ->will($this->returnValue('0'));
        $mockResponse5->expects($this->never())->method('getCardReference');
        $request5 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request5->fill($mockResponse5, GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE);
        $this->assertNull($request5->getTransactionReference());
        $this->assertNull($request5->getCardReference());

        $mockResponse6 = $mockBuilder->getMock();
        $mockResponse6->expects($this->any())->method('getTransactionReference')
            ->will($this->returnValue(null));
        $mockResponse6->expects($this->any())->method('getCardReference')
            ->will($this->returnValue(null));
        $request6 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request6->fill($mockResponse6);
        $this->assertNull($request6->getTransactionReference());
        $this->assertNull($request6->getCardReference());

        $request1->fill($mockResponse6);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request1->getCardReference());

        $mockResponse7 = $mockBuilder->getMock();
        $mockResponse7->expects($this->never())->method('getTransactionReference');
        $mockResponse7->expects($this->never())->method('getCardReference');
        $request7 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request7->fill($mockResponse7, false);
        $this->assertNull($request7->getTransactionReference());
        $this->assertNull($request7->getCardReference());

        $mockResponse8 = $mockBuilder->getMock();
        $mockResponse8->expects($this->any())->method('getTransactionReference')
            ->will($this->returnValue(''));
        $mockResponse8->expects($this->any())->method('getCardReference')
            ->will($this->returnValue('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ=='));
        $request1->fill($mockResponse8);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $request1->getCardReference());

        $request7->fill($mockResponse8, GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE);
        $this->assertNull($request7->getTransactionReference());
        $this->assertNull($request7->getCardReference());

        $request7->fill($mockResponse8, GenericPostRequest::FILL_MODE_ALL);
        $this->assertNull($request7->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $request7->getCardReference());
    }


    public function testGetDataSimple()
    {
        $this->assertSame($this->expectedData, $this->request->getData());
    }


    public function testCreateResponse()
    {
        $request = new ExtendedGenericPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $response = $request->testMethodCreateResponse(['some' => 'value'], 123);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        $this->assertSame(123, $response->getHttpResponseStatusCode());
        $this->assertSame(['some' => 'value'], $response->getData());
    }


    public function testAddCardReference()
    {
        $request = new ExtendedGenericPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $data = $this->initializationData;
        $data['cardReference'] = 'eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9';
        $this->assertSame($request, $request->initialize($data));
        $expected1 = $this->expectedData;
        $expected1['PAYMENT.CODE'] = 'AA.DB';
        $this->assertSame($expected1, $request->getData());
        $request->setAddCardReferenceMode(null);
        $this->assertSame($this->expectedData, $request->getData());
        $expected3 = $this->expectedData;
        $expected3['PAYMENT.CODE'] = 'AA.BB';
        $expected3['ACCOUNT.REGISTRATION'] = 'test2';
        $request->setAddCardReferenceMode('full');
        $this->assertSame($expected3, $request->getData());
    }
}


class ExtendedGenericPostRequestForTesting extends GenericPostRequest
{
    public function testMethodCreateResponse(array $data, $httpStatusCode)
    {
        return $this->createResponse($data, $httpStatusCode);
    }


    public function setAddCardReferenceMode($value)
    {
        $this->addCardReferenceMode = $value;
    }
}
