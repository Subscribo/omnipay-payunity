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
