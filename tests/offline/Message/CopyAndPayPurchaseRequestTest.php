<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest;

class CopyAndPayPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->options = [
            'amount' => '12.35',
            'currency' => 'EUR',
            'returnUrl' => 'https://my.site.example/redirect/url',
            'testMode' => true,
            'securitySender' => '696a8f0fabffea91517d0eb0a0bf9c33',
            'transactionChannel' => '52275ebaf361f20a76b038ba4c806991',
            'userLogin' => '1143238d620a572a726fe92eede0d1ab',
            'userPwd' => 'demo',
        ];
        $this->extendedOptions = $this->options;
        $this->extendedOptions['clientIp'] = '192.0.2.1';
        $this->extendedOptions['brands'] = 'VISA MAESTRO MASTER';
        $this->extendedOptions['presentationUsage'] = 'Just for testing';
        $this->extendedOptions['paymentMemo'] = 'Some memo';
        $this->extendedOptions['paymentType'] = 'RB';
        $this->extendedOptions['transactionMode'] = 'CONNECTOR_TEST';
        $this->extendedOptions['transactionId'] = 'Transaction:12345ABC';
        $this->extendedOptions['identificationShopperId'] = 'Shopper:22';
        $this->extendedOptions['identificationInvoiceId'] = 'Inv1234';
        $this->extendedOptions['identificationBulkId'] = 'Bulk:SomeTag';
        $this->extendedOptions['identificationReferenceId'] = '12345678abcdefgh12345678abcdefgh';

        $this->card = $this->getValidCard();
        $this->card['email'] = 'email@example.com';
        $this->card['title'] = 'DR';
        $this->card['salutation'] = 'MR';
        $this->card['gender'] = 'M';
        $this->card['birthday'] = '1974-05-20';
        $this->card['company'] = 'Company Name Inc.';
        $this->card['billingPhone'] = '(+44) 01632 960 111';
        $this->card['billingMobile'] = '+44-7700-900-222';
        $this->card['firstName'] = 'John';
        $this->card['lastName'] = 'Tester';
        $this->card['billingAddress1'] = 'Main Street 1';
        $this->card['billingAddress2'] = 'Centre';
        $this->card['billingCity'] = 'New City';
        $this->card['billingPostcode'] = 'AB1 23C';
        $this->card['billingState'] = 'AT12';
        $this->card['billingCountry'] = 'AT';
        $this->card['identificationDocumentType'] = 'PASSPORT';
        $this->card['identificationDocumentNumber'] = 'AB123 456 C7';
        $this->card['shippingFirstName'] = 'Mary';
        $this->card['shippingLastName'] = 'Shopper';
        $this->card['shippingSalutation'] = 'MS';
        $this->card['shippingTitle'] = null;
        $this->card['shippingMobile'] = '+44-7700-900-220';
        $this->card['shippingPhone'] = '(+44) 01632 960 110';
        $this->card['shippingAddress1'] = 'Main Square 2';
        $this->card['shippingAddress2'] = null;
        $this->card['shippingPostcode'] = 'XY1 23Z';
        $this->card['shippingCountry'] = 'DE';
        $this->card['shippingState'] = 'DE1';
        $this->card['shippingCity'] = 'Berlin';
    }

    public function testGetDataDefault()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($this->options);
        $data = $request->getData();
        $this->assertTrue($request->getTestMode());
        $this->assertSame('696a8f0fabffea91517d0eb0a0bf9c33', $data['SECURITY.SENDER']);
        $this->assertSame('52275ebaf361f20a76b038ba4c806991', $data['TRANSACTION.CHANNEL']);
        $this->assertSame('INTEGRATOR_TEST', $data['TRANSACTION.MODE']);
        $this->assertSame('SYNC', $data['TRANSACTION.RESPONSE']);
        $this->assertSame('1143238d620a572a726fe92eede0d1ab', $data['USER.LOGIN']);
        $this->assertSame('demo', $data['USER.PWD']);
        $this->assertSame('DB', $data['PAYMENT.TYPE']);
        $this->assertSame('12.35', $data['PRESENTATION.AMOUNT']);
        $this->assertSame('EUR', $data['PRESENTATION.CURRENCY']);
        $this->assertSame('1.0', $data['REQUEST.VERSION']);
        $this->assertArrayNotHasKey('IDENTIFICATION.TRANSACTIONID', $data);
        $this->assertArrayNotHasKey('IDENTIFICATION.SHOPPERID', $data);
        $this->assertArrayNotHasKey('IDENTIFICATION.INVOICEID', $data);
        $this->assertArrayNotHasKey('IDENTIFICATION.BULKID', $data);
        $this->assertArrayNotHasKey('IDENTIFICATION.REFERENCEID', $data);

        $this->assertArrayNotHasKey('NAME.SALUTATION', $data);
        $this->assertArrayNotHasKey('NAME.TITLE', $data);
        $this->assertArrayNotHasKey('NAME.GIVEN', $data);
        $this->assertArrayNotHasKey('NAME.FAMILY', $data);
        $this->assertArrayNotHasKey('NAME.SEX', $data);
        $this->assertArrayNotHasKey('NAME.BIRTHDATE', $data);
        $this->assertArrayNotHasKey('NAME.COMPANY', $data);
        $this->assertArrayNotHasKey('ADDRESS.STREET', $data);
        $this->assertArrayNotHasKey('ADDRESS.STREET', $data);
        $this->assertArrayNotHasKey('ADDRESS.CITY', $data);
        $this->assertArrayNotHasKey('ADDRESS.ZIP', $data);
        $this->assertArrayNotHasKey('ADDRESS.STATE', $data);
        $this->assertArrayNotHasKey('ADDRESS.COUNTRY', $data);
        $this->assertArrayNotHasKey('CONTACT.EMAIL', $data);
        $this->assertArrayNotHasKey('CONTACT.PHONE', $data);
        $this->assertArrayNotHasKey('CONTACT.MOBILE', $data);
        $this->assertArrayNotHasKey('CONTACT.IP', $data);
        $this->assertArrayNotHasKey('CUSTOMER.IDENTIFICATION.PAPER', $data);
        $this->assertArrayNotHasKey('CUSTOMER.IDENTIFICATION.VALUE', $data);

        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.NAME.GIVEN', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.NAME.FAMILY', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.COUNTRY', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.STATE', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.CITY', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.ZIP', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.STREET', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.PHONE', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.MOBILE', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.EMAIL', $data);
    }

    public function testRegistrationMode()
    {
        $options = $this->options;
        $options['registrationMode'] = true;
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);
        $data = $request->getData();
        $this->assertSame('RG.DB', $data['PAYMENT.TYPE']);
        $options['paymentType'] = 'RB';
        $request2 = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request2->initialize($options);
        $data2 = $request2->getData();
        $this->assertSame('RB', $data2['PAYMENT.TYPE']);
    }

    public function testGetDataExtended()
    {
        $options = $this->extendedOptions;
        $options['card']['country'] = 'AT';
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);
        $data = $request->getData();
        $this->assertTrue($request->getTestMode());
        $this->assertSame('696a8f0fabffea91517d0eb0a0bf9c33', $data['SECURITY.SENDER']);
        $this->assertSame('52275ebaf361f20a76b038ba4c806991', $data['TRANSACTION.CHANNEL']);
        $this->assertSame('CONNECTOR_TEST', $data['TRANSACTION.MODE']);
        $this->assertSame('SYNC', $data['TRANSACTION.RESPONSE']);
        $this->assertSame('1143238d620a572a726fe92eede0d1ab', $data['USER.LOGIN']);
        $this->assertSame('demo', $data['USER.PWD']);
        $this->assertSame('RB', $data['PAYMENT.TYPE']);
        $this->assertSame('12.35', $data['PRESENTATION.AMOUNT']);
        $this->assertSame('EUR', $data['PRESENTATION.CURRENCY']);
        $this->assertSame('Transaction:12345ABC', $data['IDENTIFICATION.TRANSACTIONID']);
        $this->assertSame('Shopper:22', $data['IDENTIFICATION.SHOPPERID']);
        $this->assertSame('Inv1234', $data['IDENTIFICATION.INVOICEID']);
        $this->assertSame('Bulk:SomeTag', $data['IDENTIFICATION.BULKID']);
        $this->assertSame('12345678abcdefgh12345678abcdefgh', $data['IDENTIFICATION.REFERENCEID']);
        $this->assertSame('1.0', $data['REQUEST.VERSION']);

        $this->assertArrayNotHasKey('NAME.SALUTATION', $data);
        $this->assertArrayNotHasKey('NAME.TITLE', $data);
        $this->assertArrayNotHasKey('NAME.GIVEN', $data);
        $this->assertArrayNotHasKey('NAME.FAMILY', $data);
        $this->assertArrayNotHasKey('NAME.SEX', $data);
        $this->assertArrayNotHasKey('NAME.BIRTHDATE', $data);
        $this->assertArrayNotHasKey('NAME.COMPANY', $data);
        $this->assertArrayNotHasKey('ADDRESS.STREET', $data);
        $this->assertArrayNotHasKey('ADDRESS.STREET', $data);
        $this->assertArrayNotHasKey('ADDRESS.CITY', $data);
        $this->assertArrayNotHasKey('ADDRESS.ZIP', $data);
        $this->assertArrayNotHasKey('ADDRESS.STATE', $data);
        $this->assertSame('AT', $data['ADDRESS.COUNTRY']);
        $this->assertArrayNotHasKey('CONTACT.EMAIL', $data);
        $this->assertArrayNotHasKey('CONTACT.PHONE', $data);
        $this->assertArrayNotHasKey('CONTACT.MOBILE', $data);
        $this->assertSame('192.0.2.1', $data['CONTACT.IP']);
        $this->assertArrayNotHasKey('CUSTOMER.IDENTIFICATION.PAPER', $data);
        $this->assertArrayNotHasKey('CUSTOMER.IDENTIFICATION.VALUE', $data);

        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.NAME.GIVEN', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.NAME.FAMILY', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.COUNTRY', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.STATE', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.CITY', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.ZIP', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.ADDRESS.STREET', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.PHONE', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.MOBILE', $data);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.EMAIL', $data);
    }

    public function testGetDataCard()
    {
        $options = $this->options;
        $options['card'] = $this->card;
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);
        $data = $request->getData();
        $this->assertTrue($request->getTestMode());
        $this->assertSame('696a8f0fabffea91517d0eb0a0bf9c33', $data['SECURITY.SENDER']);
        $this->assertSame('52275ebaf361f20a76b038ba4c806991', $data['TRANSACTION.CHANNEL']);
        $this->assertSame('INTEGRATOR_TEST', $data['TRANSACTION.MODE']);
        $this->assertSame('SYNC', $data['TRANSACTION.RESPONSE']);
        $this->assertSame('1143238d620a572a726fe92eede0d1ab', $data['USER.LOGIN']);
        $this->assertSame('demo', $data['USER.PWD']);
        $this->assertSame('DB', $data['PAYMENT.TYPE']);
        $this->assertSame('12.35', $data['PRESENTATION.AMOUNT']);
        $this->assertSame('EUR', $data['PRESENTATION.CURRENCY']);
        $this->assertSame('1.0', $data['REQUEST.VERSION']);

        $this->assertSame('MR', $data['NAME.SALUTATION']);
        $this->assertSame('DR', $data['NAME.TITLE']);
        $this->assertSame('John', $data['NAME.GIVEN']);
        $this->assertSame('Tester', $data['NAME.FAMILY']);
        $this->assertSame('M', $data['NAME.SEX']);
        $this->assertSame('1974-05-20', $data['NAME.BIRTHDATE']);
        $this->assertSame('Company Name Inc.', $data['NAME.COMPANY']);
        $this->assertStringStartsWith('Main Street 1', $data['ADDRESS.STREET']);
        $this->assertStringEndsWith('Centre', $data['ADDRESS.STREET']);
        $this->assertSame("Main Street 1\nCentre", $data['ADDRESS.STREET']);
        $this->assertSame('New City', $data['ADDRESS.CITY']);
        $this->assertSame('AB1 23C', $data['ADDRESS.ZIP']);
        $this->assertSame('AT12', $data['ADDRESS.STATE']);
        $this->assertSame('AT', $data['ADDRESS.COUNTRY']);
        $this->assertSame('email@example.com', $data['CONTACT.EMAIL']);
        $this->assertSame('(+44) 01632 960 111', $data['CONTACT.PHONE']);
        $this->assertSame('+44-7700-900-222', $data['CONTACT.MOBILE']);
        $this->assertArrayNotHasKey('CONTACT.IP', $data);
        $this->assertSame('PASSPORT', $data['CUSTOMER.IDENTIFICATION.PAPER']);
        $this->assertSame('AB123 456 C7', $data['CUSTOMER.IDENTIFICATION.VALUE']);

        $this->assertSame('Mary', $data['CUSTOMER.SHIPPING.NAME.GIVEN']);
        $this->assertSame('Shopper', $data['CUSTOMER.SHIPPING.NAME.FAMILY']);
        $this->assertSame('DE', $data['CUSTOMER.SHIPPING.ADDRESS.COUNTRY']);
        $this->assertSame('DE1', $data['CUSTOMER.SHIPPING.ADDRESS.STATE']);
        $this->assertSame('Berlin', $data['CUSTOMER.SHIPPING.ADDRESS.CITY']);
        $this->assertSame('XY1 23Z', $data['CUSTOMER.SHIPPING.ADDRESS.ZIP']);
        $this->assertSame('Main Square 2', $data['CUSTOMER.SHIPPING.ADDRESS.STREET']);
        $this->assertSame( '(+44) 01632 960 110', $data['CUSTOMER.SHIPPING.CONTACT.PHONE']);
        $this->assertSame( '+44-7700-900-220', $data['CUSTOMER.SHIPPING.CONTACT.MOBILE']);
        $this->assertArrayNotHasKey('CUSTOMER.SHIPPING.CONTACT.EMAIL', $data);
    }


    public function testGetReturnUrl()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($this->options);
        $this->assertSame('https://my.site.example/redirect/url', $request->getReturnUrl());
    }

    public function testGetBrands()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($this->extendedOptions);
        $this->assertSame('VISA MAESTRO MASTER', $request->getBrands());
    }

    public function testSetBrands()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getBrands());
        $request->setBrands('');
        $this->assertSame('', $request->getBrands());
        $request->setBrands([]);
        $this->assertSame([], $request->getBrands());
        $request->setBrands('VISA');
        $this->assertSame('VISA', $request->getBrands());
        $request->setBrands(['VISA']);
        $this->assertSame(['VISA'], $request->getBrands());
        $request->setBrands('VISA MASTER');
        $this->assertSame('VISA MASTER', $request->getBrands());
        $request->setBrands(['VISA', 'MAESTRO', "MASTER"]);
        $this->assertSame(['VISA', "MAESTRO", 'MASTER'], $request->getBrands());
    }

    public function testSetPresentationUsage()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getPresentationUsage());
        $value = uniqid();
        $this->assertSame($request, $request->setPresentationUsage($value));
        $this->assertSame($value, $request->getPresentationUsage());
    }

    public function testSetPaymentMemo()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getPaymentMemo());
        $value = uniqid();
        $this->assertSame($request, $request->setPaymentMemo($value));
        $this->assertSame($value, $request->getPaymentMemo());
    }

    public function testSetPaymentType()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getPaymentType());
        $value = uniqid();
        $this->assertSame($request, $request->setPaymentType($value));
        $this->assertSame($value, $request->getPaymentType($value));
    }

    public function testSetIdentificationReferenceId()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getIdentificationReferenceId());
        $value = uniqid();
        $this->assertSame($request, $request->setIdentificationReferenceId($value));
        $this->assertSame($value, $request->getIdentificationReferenceId());
        $this->assertSame($value, $request->getCardReference());
    }

    public function testSetCardReference()
    {
        $request =  new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getCardReference());
        $value = uniqid();
        $request->initialize(['cardReference' => $value]);
        $this->assertSame($value, $request->getCardReference());
        $this->assertSame($value, $request->getIdentificationReferenceId());
        $this->assertSame($request, $request->setCardReference(null));
        $this->assertNull($request->getCardReference());
        $this->assertNull($request->getIdentificationReferenceId());
    }
}
