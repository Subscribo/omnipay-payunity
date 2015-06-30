<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\AbstractPostRequest;
use Omnipay\PayUnity\Message\GenericPostResponse;


class AbstractPostRequestTest extends TestCase
{
    protected function getTestCardData()
    {
        $card = [];
        $card['email'] = 'email@example.com';
        $card['title'] = 'DR';
        $card['salutation'] = 'MR';
        $card['gender'] = 'M';
        $card['birthday'] = '1974-05-20';
        $card['company'] = 'Company Name Inc.';
        $card['billingPhone'] = '(+44) 01632 960 111';
        $card['billingMobile'] = '+44-7700-900-222';
        $card['firstName'] = 'John';
        $card['lastName'] = 'Tester';
        $card['billingAddress1'] = 'Main Street 1';
        $card['billingAddress2'] = 'Centre';
        $card['billingCity'] = 'New City';
        $card['billingPostcode'] = 'AB1 23C';
        $card['billingState'] = 'AT12';
        $card['billingCountry'] = 'AT';

        $card['identificationDocumentType'] = 'PASSPORT';
        $card['identificationDocumentNumber'] = 'AB123 456 C7';
        $card['shippingFirstName'] = 'Mary';
        $card['shippingLastName'] = 'Shopper';
        $card['shippingSalutation'] = 'MS';
        $card['shippingTitle'] = null;
        $card['shippingMobile'] = '+44-7700-900-220';
        $card['shippingPhone'] = '(+44) 01632 960 110';
        $card['shippingAddress1'] = 'Main Square 2';
        $card['shippingAddress2'] = null;
        $card['shippingPostcode'] = 'XY1 23Z';
        $card['shippingCountry'] = 'DE';
        $card['shippingState'] = 'DE1';
        $card['shippingCity'] = 'Berlin';
        return $card;
    }


    public function testGettersAndSetters()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $this->assertEmpty($request->getTestMode());
        $this->assertSame($request, $request->setTestMode(true));
        $this->assertTrue($request->getTestMode());

        $this->assertNull($request->getTransactionId());
        $this->assertNull($request->getTransactionReference());
        $this->assertNull($request->getIdentificationTransactionId());
        $this->assertNull($request->getIdentificationReferenceId());
        $this->assertNull($request->getPaymentMemo());
        $this->assertNull($request->getPresentationUsage());
        $this->assertNull($request->getPaymentType());
        $this->assertNull($request->getPaymentMethod());
        $this->assertNull($request->getAmount());
        $this->assertNull($request->getCurrency());
        $this->assertNull($request->getCardReference());
        $this->assertNull($request->getCard());

        $this->assertSame($request, $request->setTransactionId('Some transaction ID'));
        $this->assertSame('Some transaction ID', $request->getTransactionId());
        $this->assertSame('Some transaction ID', $request->getIdentificationTransactionId());
        $this->assertSame($request, $request->setIdentificationTransactionId('Some identification transaction ID'));
        $this->assertSame('Some identification transaction ID', $request->getTransactionId());
        $this->assertSame('Some identification transaction ID', $request->getIdentificationTransactionId());

        $this->assertSame($request, $request->setTransactionReference('some reference'));
        $this->assertSame('some reference', $request->getTransactionReference());
        $this->assertSame('some reference', $request->getIdentificationReferenceId());
        $this->assertSame($request, $request->setIdentificationReferenceId('some identification reference ID'));
        $this->assertSame('some identification reference ID', $request->getTransactionReference());
        $this->assertSame('some identification reference ID', $request->getIdentificationReferenceId());

        $this->assertSame($request, $request->setCardReference('some card reference'));
        $this->assertSame('some card reference', $request->getCardReference());

        $this->assertSame($request, $request->setPresentationUsage('some usage'));
        $this->assertSame('some usage', $request->getPresentationUsage());

        $this->assertSame($request, $request->setPaymentType('AA'));
        $this->assertSame('AA', $request->getPaymentType());

        $this->assertSame($request, $request->setPaymentMethod('BB'));
        $this->assertSame('BB', $request->getPaymentMethod());

        $this->assertSame($request, $request->setPaymentMemo('some memo'));
        $this->assertSame('some memo', $request->getPaymentMemo());


        $this->assertSame($request, $request->setIdentificationTransactionId(null));
        $this->assertSame($request, $request->setTransactionReference(null));
        $this->assertSame($request, $request->setCardReference(null));
        $this->assertSame($request, $request->setPresentationUsage(null));
        $this->assertSame($request, $request->setPaymentType(null));
        $this->assertSame($request, $request->setPaymentMethod(null));
        $this->assertSame($request, $request->setPaymentMemo(null));

        $this->assertNull($request->getTransactionId());
        $this->assertNull($request->getTransactionReference());
        $this->assertNull($request->getIdentificationTransactionId());
        $this->assertNull($request->getIdentificationReferenceId());
        $this->assertNull($request->getPaymentMemo());
        $this->assertNull($request->getPresentationUsage());
        $this->assertNull($request->getPaymentType());
        $this->assertNull($request->getPaymentMethod());
        $this->assertNull($request->getCardReference());
    }


    public function testEndpointUrl()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $this->assertSame('https://payunity.com/frontend/payment.prc', $request->testMethodGetEndpointUrl([]));
        $request->setTestMode(true);
        $this->assertSame('https://test.payunity.com/frontend/payment.prc', $request->testMethodGetEndpointUrl([]));
    }


    public function testHttpRequestHeaders()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $expected = ['Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'];
        $this->assertSame($expected, $request->testMethodGetHttpRequestHeaders([]));
    }


    public function testChooseTransactionMode()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $this->assertSame('LIVE', $request->testMethodChooseTransactionMode());
        $request->setTestMode(true);
        $this->assertSame('INTEGRATOR_TEST', $request->testMethodChooseTransactionMode());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The transactionChannel parameter is required
     */
    public function testPrepareDataValidation()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $request->setTestMode(true);
        $request->setSecuritySender('some value');
        $request->getData();
    }


    public function testPrepareDataSimple()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'testMode' => true,
            'securitySender' => 'some security sender',
            'transactionChannel' => 'some transaction channel',
            'userLogin' => 'some username',
            'userPwd' => 'some password',
        ]);
        $expected = [
            'REQUEST.VERSION' => '1.0',
            'SECURITY.SENDER' => 'some security sender',
            'TRANSACTION.CHANNEL' => 'some transaction channel',
            'TRANSACTION.MODE' => 'INTEGRATOR_TEST',
            'TRANSACTION.RESPONSE' => 'SYNC',
            'USER.LOGIN' => 'some username',
            'USER.PWD' => 'some password',
        ];
        $this->assertSame($expected, $request->getData());
    }


    public function testPrepareDataExtended()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'testMode' => true,
            'securitySender' => 'some security sender',
            'transactionChannel' => 'some transaction channel',
            'userLogin' => 'some username',
            'userPwd' => 'some password',
            'transactionId' => 'some transaction ID',
            'transactionReference' => 'some transaction reference',
            'amount' => '1.23',
            'currency' => 'EUR',
            'paymentMemo' => 'some memo',
            'clientIp' => '192.0.2.1',
            'presentationUsage' => 'Just for testing',
            'paymentType' => 'AA',
            'transactionMode' => 'CONNECTOR_TEST',
            'identificationShopperId' => 'some shopper ID',
            'identificationInvoiceId' => 'some invoice ID',
            'identificationBulkId' => 'some bulk ID',
        ]);
        $expected = [
            'REQUEST.VERSION' => '1.0',
            'SECURITY.SENDER' => 'some security sender',
            'TRANSACTION.CHANNEL' => 'some transaction channel',
            'TRANSACTION.MODE' => 'CONNECTOR_TEST',
            'TRANSACTION.RESPONSE' => 'SYNC',
            'USER.LOGIN' => 'some username',
            'USER.PWD' => 'some password',
            'PAYMENT.MEMO' => 'some memo',
            'CONTACT.IP' => '192.0.2.1',
            'PRESENTATION.AMOUNT' => '1.23',
            'PRESENTATION.CURRENCY' => 'EUR',
            'PRESENTATION.USAGE' => 'Just for testing',
            'IDENTIFICATION.TRANSACTIONID' => 'some transaction ID',
            'IDENTIFICATION.SHOPPERID' => 'some shopper ID',
            'IDENTIFICATION.INVOICEID' => 'some invoice ID',
            'IDENTIFICATION.BULKID' => 'some bulk ID',
            'IDENTIFICATION.REFERENCEID' => 'some transaction reference',
        ];
        $this->assertSame($expected, $request->getData());
    }


    public function testPrepareDataWithCard()
    {
        $request = new ExtendedAbstractPostRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'testMode' => true,
            'securitySender' => 'some security sender',
            'transactionChannel' => 'some transaction channel',
            'userLogin' => 'some username',
            'userPwd' => 'some password',
            'card' => $this->getTestCardData(),
        ]);
        $expected = [
            'REQUEST.VERSION' => '1.0',
            'SECURITY.SENDER' => 'some security sender',
            'TRANSACTION.CHANNEL' => 'some transaction channel',
            'TRANSACTION.MODE' => 'INTEGRATOR_TEST',
            'TRANSACTION.RESPONSE' => 'SYNC',
            'USER.LOGIN' => 'some username',
            'USER.PWD' => 'some password',
            'NAME.GIVEN' => 'John',
            'NAME.FAMILY' => 'Tester',
            'NAME.SALUTATION' => 'MR',
            'NAME.TITLE' => 'DR',
            'NAME.SEX' => 'M',
            'NAME.BIRTHDATE' => '1974-05-20',
            'NAME.COMPANY' => 'Company Name Inc.',
            'ADDRESS.COUNTRY' => 'AT',
            'ADDRESS.STATE' => 'AT12',
            'ADDRESS.CITY' => 'New City',
            'ADDRESS.ZIP' => 'AB1 23C',
            'ADDRESS.STREET' => "Main Street 1\nCentre",
            'CONTACT.EMAIL' => 'email@example.com',
            'CONTACT.PHONE' => '(+44) 01632 960 111',
            'CONTACT.MOBILE' => '+44-7700-900-222',
            'CUSTOMER.IDENTIFICATION.PAPER' => 'PASSPORT',
            'CUSTOMER.IDENTIFICATION.VALUE' => 'AB123 456 C7',
            'CUSTOMER.SHIPPING.NAME.GIVEN' => 'Mary',
            'CUSTOMER.SHIPPING.NAME.FAMILY' => 'Shopper',
            'CUSTOMER.SHIPPING.ADDRESS.STREET' => "Main Square 2",
            'CUSTOMER.SHIPPING.ADDRESS.CITY' => 'Berlin',
            'CUSTOMER.SHIPPING.ADDRESS.ZIP' => 'XY1 23Z',
            'CUSTOMER.SHIPPING.ADDRESS.STATE' => 'DE1',
            'CUSTOMER.SHIPPING.ADDRESS.COUNTRY' => 'DE',
            'CUSTOMER.SHIPPING.ADDRESS.STREET' => 'Main Square 2',
            'CUSTOMER.SHIPPING.CONTACT.PHONE' => '(+44) 01632 960 110',
            'CUSTOMER.SHIPPING.CONTACT.MOBILE' => '+44-7700-900-220',
        ];
        $this->assertSame($expected, $request->getData());
    }
}


class ExtendedAbstractPostRequestForTesting extends AbstractPostRequest
{
    public function getData()
    {
        return $this->prepareData();
    }

    /**
     * @param array $data
     * @param int $httpStatusCode
     * @return \Omnipay\Common\Message\ResponseInterface|GenericPostResponse
     */
    protected function createResponse(array $data, $httpStatusCode)
    {
        return new GenericPostResponse($this, $data, $httpStatusCode);
    }

    public function testMethodGetEndpointUrl($data)
    {
        return $this->getEndpointUrl($data);
    }

    public function testMethodGetHttpRequestHeaders($data)
    {
        return $this->getHttpRequestHeaders($data);
    }

    public function testMethodChooseTransactionMode()
    {
        return $this->chooseTransactionMode();
    }
}
