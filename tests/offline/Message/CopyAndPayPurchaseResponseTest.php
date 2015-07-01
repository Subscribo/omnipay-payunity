<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

class CopyAndPayPurchaseResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'amount' => '12.35',
            'currency' => 'EUR',
        ]);
        $this->request->setTestMode(true);
    }


    public function testTransactionToken()
    {
        $response = new CopyAndPayPurchaseResponse(
            $this->request,
            ['transaction' => ['token' => 'A550D17DC663DFA8973CCAB8A117669A.sbg-vm-fe01']],
            200
        );
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isCancelled());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $widget = $response->getWidget();
        $this->assertNotEmpty($widget);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $this->assertSame('A550D17DC663DFA8973CCAB8A117669A.sbg-vm-fe01', $response->getTransactionToken());
        $this->assertNull($response->getTransactionReference());
    }


    public function testEmptyTransactionToken()
    {
        $response = new CopyAndPayPurchaseResponse($this->request, [], 200);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidget());
        $this->assertNull($response->getTransactionToken());
        $this->assertNull($response->getTransactionReference());
    }
}
