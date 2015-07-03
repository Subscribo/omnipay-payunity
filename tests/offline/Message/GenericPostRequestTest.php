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
        /*   Constants and their combinations */

        $this->assertSame(GenericPostRequest::FILL_MODE_ALL, GenericPostRequest::FILL_MODE_REFERENCES_AND_PRESENTATION);
        $this->assertSame(GenericPostRequest::FILL_MODE_REFERENCES,
            (GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE | GenericPostRequest::FILL_MODE_CARD_REFERENCE ));
        $this->assertSame(GenericPostRequest::FILL_MODE_PRESENTATION,
            (GenericPostRequest::FILL_MODE_AMOUNT | GenericPostRequest::FILL_MODE_CURRENCY | GenericPostRequest::FILL_MODE_DESCRIPTION));
        $this->assertSame(GenericPostRequest::FILL_MODE_REFERENCES_AND_PRESENTATION,
            (GenericPostRequest::FILL_MODE_REFERENCES | GenericPostRequest::FILL_MODE_PRESENTATION ));
        $this->assertSame(GenericPostRequest::FILL_MODE_ALL,
            (GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE | GenericPostRequest::FILL_MODE_CARD_REFERENCE
            | GenericPostRequest::FILL_MODE_AMOUNT | GenericPostRequest::FILL_MODE_CURRENCY | GenericPostRequest::FILL_MODE_DESCRIPTION));

        /*   Preparation,  FILL_MODE_ALL, FILL_MODE_REFERENCES_AND_PRESENTATION  */

        $mockBuilder = $this->getMockBuilder('\\Omnipay\\PayUnity\\Message\\GenericPostResponse')
            ->disableOriginalConstructor();

        $requestA = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());

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
        $requestA->fill($mockResponseA, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('a_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $requestA->getCardReference());
        $this->assertSame('5.25', $requestA->getAmount());
        $this->assertSame('EUR', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        $requestB = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $requestB->fill($mockResponseA, GenericPostRequest::FILL_MODE_REFERENCES_AND_PRESENTATION);
        $this->assertEquals($requestA, $requestB);

        /* FILL_MODE_REFERENCES, default */

        $request1 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request1->getTransactionReference());
        $this->assertNull($request1->getCardReference());
        $this->assertNull($request1->getAmount());
        $this->assertNull($request1->getCurrency());
        $this->assertNull($request1->getDescription());

        $mockResponse1 = $mockBuilder->getMock();
        $mockResponse1->expects($this->exactly(3))->method('getTransactionReference')
            ->will($this->returnValue('some_transaction_reference'));
        $mockResponse1->expects($this->exactly(3))->method('getCardReference')
            ->will($this->returnValue('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $mockResponse1->expects($this->never())->method('getPresentationAmount');
        $mockResponse1->expects($this->never())->method('getPresentationCurrency');
        $mockResponse1->expects($this->never())->method('getPresentationUsage');
        $request1->fill($mockResponse1);
        $this->assertSame('some_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request1->getCardReference());
        $this->assertNull($request1->getAmount());
        $this->assertNull($request1->getCurrency());
        $this->assertNull($request1->getDescription());

        $requestB->fill($mockResponse1, GenericPostRequest::FILL_MODE_REFERENCES);
        $this->assertSame('some_transaction_reference', $requestB->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $requestB->getCardReference());
        $this->assertSame('5.25', $requestB->getAmount());
        $this->assertSame('EUR', $requestB->getCurrency());
        $this->assertSame('Test presentation usage', $requestB->getDescription());

        $requestC = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $requestC->fill($mockResponse1, GenericPostRequest::FILL_MODE_REFERENCES);
        $this->assertEquals($request1, $requestC);

        /*  FILL_MODE_ALL, FILL_MODE_REFERENCES_AND_PRESENTATION  */

        $mockResponse2 = $mockBuilder->getMock();
        $mockResponse2->expects($this->atLeastOnce())->method('getTransactionReference')
            ->will($this->returnValue('another_transaction_reference'));
        $mockResponse2->expects($this->atLeastOnce())->method('getCardReference')
            ->will($this->returnValue(''));
        $mockResponse2->expects($this->atLeastOnce())->method('getPresentationAmount')
            ->will($this->returnValue('0.00'));
        $mockResponse2->expects($this->atLeastOnce())->method('getPresentationCurrency')
            ->will($this->returnValue(''));
        $mockResponse2->expects($this->atLeastOnce())->method('getPresentationUsage')
            ->will($this->returnValue(''));
        $request2 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request2->fill($mockResponse2, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('another_transaction_reference', $request2->getTransactionReference());
        $this->assertNull($request2->getCardReference());
        $this->assertNull($request2->getAmount());
        $this->assertNull($request2->getCurrency());
        $this->assertNull($request2->getDescription());
        $requestA->fill($mockResponse2, GenericPostRequest::FILL_MODE_REFERENCES_AND_PRESENTATION);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $requestA->getCardReference());
        $this->assertSame('5.25', $requestA->getAmount());
        $this->assertSame('EUR', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        $request1->fill($mockResponse2);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request1->getCardReference());
        $this->assertNull($request1->getAmount());
        $this->assertNull($request1->getCurrency());
        $this->assertNull($request1->getDescription());

        /* FILL_MODE_TRANSACTION_REFERENCE */

        $mockResponse3 = $mockBuilder->getMock();
        $mockResponse3->expects($this->once())->method('getTransactionReference')
            ->will($this->returnValue('some_transaction_reference'));
        $mockResponse3->expects($this->never())->method('getCardReference');
        $mockResponse3->expects($this->never())->method('getPresentationAmount');
        $mockResponse3->expects($this->never())->method('getPresentationCurrency');
        $mockResponse3->expects($this->never())->method('getPresentationUsage');
        $request3 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request3->fill($mockResponse3, GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE);
        $this->assertSame('some_transaction_reference', $request3->getTransactionReference());
        $this->assertNull($request3->getCardReference());
        $this->assertNull($request3->getAmount());
        $this->assertNull($request3->getCurrency());
        $this->assertNull($request3->getDescription());

        /* FILL_MODE_CARD_REFERENCE */

        $mockResponse4 = $mockBuilder->getMock();
        $mockResponse4->expects($this->never())->method('getTransactionReference');
        $mockResponse4->expects($this->once())->method('getCardReference')
            ->will($this->returnValue('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9'));
        $mockResponse4->expects($this->never())->method('getPresentationAmount');
        $mockResponse4->expects($this->never())->method('getPresentationCurrency');
        $mockResponse4->expects($this->never())->method('getPresentationUsage');
        $request4 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request4->fill($mockResponse4, GenericPostRequest::FILL_MODE_CARD_REFERENCE);
        $this->assertNull($request4->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request4->getCardReference());
        $this->assertNull($request4->getAmount());
        $this->assertNull($request4->getCurrency());
        $this->assertNull($request4->getDescription());

        /* FILL_MODE_TRANSACTION_REFERENCE with empty value */

        $mockResponse5 = $mockBuilder->getMock();
        $mockResponse5->expects($this->once())->method('getTransactionReference')
            ->will($this->returnValue('0'));
        $mockResponse5->expects($this->never())->method('getCardReference');
        $mockResponse5->expects($this->never())->method('getPresentationAmount');
        $mockResponse5->expects($this->never())->method('getPresentationCurrency');
        $mockResponse5->expects($this->never())->method('getPresentationUsage');
        $request5 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request5->fill($mockResponse5, GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE);
        $this->assertNull($request5->getTransactionReference());
        $this->assertNull($request5->getCardReference());
        $this->assertNull($request5->getAmount());
        $this->assertNull($request5->getCurrency());
        $this->assertNull($request5->getDescription());

        /* FILL_MODE_ALL null values */

        $mockResponse6 = $mockBuilder->getMock();
        $mockResponse6->expects($this->exactly(3))->method('getTransactionReference')
            ->will($this->returnValue(null));
        $mockResponse6->expects($this->exactly(3))->method('getCardReference')
            ->will($this->returnValue(null));
        $mockResponse6->expects($this->exactly(3))->method('getPresentationAmount')
            ->will($this->returnValue(null));
        $mockResponse6->expects($this->exactly(3))->method('getPresentationCurrency')
            ->will($this->returnValue(null));
        $mockResponse6->expects($this->exactly(3))->method('getPresentationUsage')
            ->will($this->returnValue(null));
        $request6 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request6->fill($mockResponse6, GenericPostRequest::FILL_MODE_ALL);
        $this->assertNull($request6->getTransactionReference());
        $this->assertNull($request6->getCardReference());
        $this->assertNull($request6->getAmount());
        $this->assertNull($request6->getCurrency());
        $this->assertNull($request6->getDescription());
        $requestA->fill($mockResponse6, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $requestA->getCardReference());
        $this->assertSame('5.25', $requestA->getAmount());
        $this->assertSame('EUR', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        $request1->fill($mockResponse6, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $request1->getCardReference());
        $this->assertNull($request1->getAmount());
        $this->assertNull($request1->getCurrency());
        $this->assertNull($request1->getDescription());

        /* fillMode false - no filling */

        $mockResponse7 = $mockBuilder->getMock();
        $mockResponse7->expects($this->never())->method('getTransactionReference');
        $mockResponse7->expects($this->never())->method('getCardReference');
        $mockResponse7->expects($this->never())->method('getPresentationAmount');
        $mockResponse7->expects($this->never())->method('getPresentationCurrency');
        $mockResponse7->expects($this->never())->method('getPresentationUsage');
        $request7 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request7->fill($mockResponse7, false);
        $this->assertNull($request7->getTransactionReference());
        $this->assertNull($request7->getCardReference());
        $this->assertNull($request7->getAmount());
        $this->assertNull($request7->getCurrency());
        $this->assertNull($request7->getDescription());

        /* Empty and non empty values, different modes */

        $mockResponse8 = $mockBuilder->getMock();
        $mockResponse8->expects($this->atLeastOnce())->method('getTransactionReference')
            ->will($this->returnValue(''));
        $mockResponse8->expects($this->atLeastOnce())->method('getCardReference')
            ->will($this->returnValue('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ=='));
        $mockResponse8->expects($this->atLeastOnce())->method('getPresentationAmount')
            ->will($this->returnValue('0'));
        $mockResponse8->expects($this->atLeastOnce())->method('getPresentationCurrency')
            ->will($this->returnValue('GBP'));
        $mockResponse8->expects($this->atLeastOnce())->method('getPresentationUsage')
            ->will($this->returnValue(''));
        $request1->fill($mockResponse8);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $request1->getCardReference());
        $this->assertNull($request1->getAmount());
        $this->assertNull($request1->getCurrency());
        $this->assertNull($request1->getDescription());

        $request1->fill($mockResponse8, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('another_transaction_reference', $request1->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $request1->getCardReference());
        $this->assertNull($request1->getAmount());
        $this->assertSame('GBP', $request1->getCurrency());
        $this->assertNull($request1->getDescription());

        $request7->fill($mockResponse8, GenericPostRequest::FILL_MODE_TRANSACTION_REFERENCE);
        $this->assertNull($request7->getTransactionReference());
        $this->assertNull($request7->getCardReference());
        $this->assertNull($request7->getAmount());
        $this->assertNull($request7->getCurrency());
        $this->assertNull($request7->getDescription());

        $request7->fill($mockResponse8, GenericPostRequest::FILL_MODE_ALL);
        $this->assertNull($request7->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $request7->getCardReference());
        $this->assertNull($request7->getAmount());
        $this->assertSame('GBP', $request7->getCurrency());
        $this->assertNull($request7->getDescription());

        $requestA->fill($mockResponse8, GenericPostRequest::FILL_MODE_ALL);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $requestA->getCardReference());
        $this->assertSame('5.25', $requestA->getAmount());
        $this->assertSame('GBP', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        /* FILL_MODE_AMOUNT */

        $mockResponse9 = $mockBuilder->getMock();
        $mockResponse9->expects($this->never())->method('getTransactionReference');
        $mockResponse9->expects($this->never())->method('getCardReference');
        $mockResponse9->expects($this->exactly(2))->method('getPresentationAmount')
            ->will($this->returnValue('8.15'));
        $mockResponse9->expects($this->never())->method('getPresentationCurrency');
        $mockResponse9->expects($this->never())->method('getPresentationUsage');
        $request9 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request9->fill($mockResponse9, GenericPostRequest::FILL_MODE_AMOUNT);
        $this->assertNull($request9->getTransactionReference());
        $this->assertNull($request9->getCardReference());
        $this->assertSame('8.15', $request9->getAmount());
        $this->assertNull($request9->getCurrency());
        $this->assertNull($request9->getDescription());

        $requestA->fill($mockResponse9, GenericPostRequest::FILL_MODE_AMOUNT);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $requestA->getCardReference());
        $this->assertSame('8.15', $requestA->getAmount());
        $this->assertSame('GBP', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        /* FILL_MODE_CURRENCY */

        $mockResponse10 = $mockBuilder->getMock();
        $mockResponse10->expects($this->never())->method('getTransactionReference');
        $mockResponse10->expects($this->never())->method('getCardReference');
        $mockResponse10->expects($this->never())->method('getPresentationAmount');
        $mockResponse10->expects($this->exactly(2))->method('getPresentationCurrency')
            ->will($this->returnValue('USD'));
        $mockResponse10->expects($this->never())->method('getPresentationUsage');
        $request10 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request10->fill($mockResponse10, GenericPostRequest::FILL_MODE_CURRENCY);
        $this->assertNull($request10->getTransactionReference());
        $this->assertNull($request10->getCardReference());
        $this->assertNull($request10->getAmount());
        $this->assertSame('USD', $request10->getCurrency());
        $this->assertNull($request10->getDescription());

        $requestA->fill($mockResponse10, GenericPostRequest::FILL_MODE_CURRENCY);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $requestA->getCardReference());
        $this->assertSame('8.15', $requestA->getAmount());
        $this->assertSame('USD', $requestA->getCurrency());
        $this->assertSame('Test presentation usage', $requestA->getDescription());

        /* FILL_MODE_DESCRIPTION */

        $mockResponse11 = $mockBuilder->getMock();
        $mockResponse11->expects($this->never())->method('getTransactionReference');
        $mockResponse11->expects($this->never())->method('getCardReference');
        $mockResponse11->expects($this->never())->method('getPresentationAmount');
        $mockResponse11->expects($this->never())->method('getPresentationCurrency');
        $mockResponse11->expects($this->exactly(2))->method('getPresentationUsage')
            ->will($this->returnValue('Another usage'));

        $request11 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request11->fill($mockResponse11, GenericPostRequest::FILL_MODE_DESCRIPTION);
        $this->assertNull($request11->getTransactionReference());
        $this->assertNull($request11->getCardReference());
        $this->assertNull($request11->getAmount());
        $this->assertNull($request11->getCurrency());
        $this->assertSame('Another usage', $request11->getDescription());

        $requestA->fill($mockResponse11, GenericPostRequest::FILL_MODE_DESCRIPTION);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $requestA->getCardReference());
        $this->assertSame('8.15', $requestA->getAmount());
        $this->assertSame('USD', $requestA->getCurrency());
        $this->assertSame('Another usage', $requestA->getDescription());

        /* FILL_MODE_PRESENTATION */

        $mockResponse12 = $mockBuilder->getMock();
        $mockResponse12->expects($this->never())->method('getTransactionReference');
        $mockResponse12->expects($this->never())->method('getCardReference');
        $mockResponse12->expects($this->exactly(2))->method('getPresentationAmount')
            ->will($this->returnValue('3.48'));
        $mockResponse12->expects($this->exactly(2))->method('getPresentationCurrency')
            ->will($this->returnValue('NOK'));
        $mockResponse12->expects($this->exactly(2))->method('getPresentationUsage')
            ->will($this->returnValue('Different usage'));

        $request12 = new GenericPostRequest($this->getHttpClient(), $this->getHttpRequest());
        $request12->fill($mockResponse12, GenericPostRequest::FILL_MODE_PRESENTATION);
        $this->assertNull($request12->getTransactionReference());
        $this->assertNull($request12->getCardReference());
        $this->assertSame('3.48', $request12->getAmount());
        $this->assertSame('NOK', $request12->getCurrency());
        $this->assertSame('Different usage', $request12->getDescription());

        $requestA->fill($mockResponse12, GenericPostRequest::FILL_MODE_PRESENTATION);
        $this->assertSame('another_transaction_reference', $requestA->getTransactionReference());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $requestA->getCardReference());
        $this->assertSame('3.48', $requestA->getAmount());
        $this->assertSame('NOK', $requestA->getCurrency());
        $this->assertSame('Different usage', $requestA->getDescription());
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
        $expected3['PAYMENT.CODE'] = 'AA.DB';
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
