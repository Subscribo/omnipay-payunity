<?php

namespace Omnipay\PayUnity;

use PHPUnit_Framework_TestCase;
use Omnipay\PayUnity\AccountRegistrationReference;

class AccountRegistrationReferenceTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $reference = new AccountRegistrationReference('test');
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\AccountRegistrationReference', $reference);
        /** @var $reference \Omnipay\PayUnity\AccountRegistrationReference */
        $this->assertSame('test', $reference->accountRegistration);
        $this->assertNull($reference->paymentCode);
        $this->assertFalse($reference->isLoaded());
        $this->assertNull($reference->export());

        $reference2 = new AccountRegistrationReference('test2', 'AA.BB');
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\AccountRegistrationReference', $reference2);
        /** @var $reference2 \Omnipay\PayUnity\AccountRegistrationReference */
        $this->assertSame('test2', $reference2->accountRegistration);
        $this->assertSame('AA.BB', $reference2->paymentCode);
        $this->assertTrue($reference2->isLoaded());
        $this->assertSame('eyJhciI6InRlc3QyIiwicGMiOiJBQS5CQiJ9', $reference2->export());
    }


    public function testRebuildFromEmpty()
    {
        $this->assertNull(AccountRegistrationReference::rebuild(''));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Provided parameter does not contain valid encoded AccountRegistrationReference
     */
    public function testRebuildFromInvalid()
    {
        AccountRegistrationReference::rebuild('invalid');
    }


    public function testRebuildSuccess()
    {
        $reference = AccountRegistrationReference::rebuild('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=');
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\AccountRegistrationReference', $reference);
        /** @var $reference \Omnipay\PayUnity\AccountRegistrationReference */
        $this->assertSame('test', $reference->accountRegistration);
        $this->assertSame('CC.DB', $reference->paymentCode);
        $this->assertTrue($reference->isLoaded());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $reference->export());
    }


    public function testEmpty()
    {
        $reference = new AccountRegistrationReference();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\AccountRegistrationReference', $reference);
        /** @var $reference \Omnipay\PayUnity\AccountRegistrationReference */
        $this->assertNull($reference->accountRegistration);
        $this->assertNull($reference->paymentCode);
        $this->assertFalse($reference->isLoaded());
        $this->assertNull($reference->export());
    }


    public function testImport()
    {
        $reference = new AccountRegistrationReference();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\AccountRegistrationReference', $reference);
        /** @var $reference \Omnipay\PayUnity\AccountRegistrationReference */
        $this->assertFalse($reference->isLoaded());
        $reference->import(null);
        $this->assertNull($reference->accountRegistration);
        $this->assertNull($reference->paymentCode);
        $reference->import('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=');
        $this->assertSame('test', $reference->accountRegistration);
        $this->assertSame('CC.DB', $reference->paymentCode);
        $this->assertTrue($reference->isLoaded());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $reference->export());
        $reference->import('');
        $this->assertSame('test', $reference->accountRegistration);
        $this->assertSame('CC.DB', $reference->paymentCode);
        $this->assertTrue($reference->isLoaded());
        $this->assertSame('eyJhciI6InRlc3QiLCJwYyI6IkNDLkRCIn0=', $reference->export());
        $reference->import('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==');
        $this->assertSame('different', $reference->accountRegistration);
        $this->assertSame('DD.DB', $reference->paymentCode);
        $this->assertTrue($reference->isLoaded());
        $this->assertSame('eyJhciI6ImRpZmZlcmVudCIsInBjIjoiREQuREIifQ==', $reference->export());
    }


    public function testIsLoaded()
    {
        $reference = new AccountRegistrationReference();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\AccountRegistrationReference', $reference);
        /** @var $reference \Omnipay\PayUnity\AccountRegistrationReference */
        $this->assertNull($reference->accountRegistration);
        $this->assertNull($reference->paymentCode);
        $this->assertFalse($reference->isLoaded());
        $this->assertNull($reference->export());
        $reference->paymentCode = 'AA.BB';
        $this->assertNull($reference->accountRegistration);
        $this->assertSame('AA.BB', $reference->paymentCode);
        $this->assertFalse($reference->isLoaded());
        $this->assertNull($reference->export());
        $reference->accountRegistration = 'someregistration';
        $this->assertSame('someregistration', $reference->accountRegistration);
        $this->assertSame('AA.BB', $reference->paymentCode);
        $this->assertTrue($reference->isLoaded());
        $this->assertSame('eyJhciI6InNvbWVyZWdpc3RyYXRpb24iLCJwYyI6IkFBLkJCIn0=', $reference->export());
        $reference->paymentCode = '';
        $this->assertSame('someregistration', $reference->accountRegistration);
        $this->assertSame('', $reference->paymentCode);
        $this->assertFalse($reference->isLoaded());
        $this->assertNull($reference->export());
    }
}
