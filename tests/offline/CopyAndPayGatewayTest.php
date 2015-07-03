<?php

namespace Omnipay\PayUnity;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\COPYandPAYGateway;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;
use Symfony\Component\HttpFoundation\Request;

class CopyAndPayGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new COPYandPAYGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);

        $this->gateway->setSecuritySender('696a8f0fabffea91517d0eb0a0bf9c33');
        $this->gateway->setTransactionChannel('52275ebaf361f20a76b038ba4c806991');
        $this->gateway->setUserLogin('1143238d620a572a726fe92eede0d1ab');
        $this->gateway->setUserPwd('demo');
        $this->gateway->setIdentificationShopperId('Shopper 13245');
        $this->options = array(
            'amount' => '10.00',
            'currency' => 'EUR',
        );
        $this->connectorModeGateway = new COPYandPAYGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->connectorModeGateway->initialize([
            "securitySender" => "696a8f0fabffea91517d0eb0a0bf9c33",
            "transactionChannel" => "52275ebaf361f20a76b038ba4c806991",
            "transactionMode" => "CONNECTOR_TEST",
            "userLogin" => "1143238d620a572a726fe92eede0d1ab",
            "userPwd" => "demo",
            "testMode" => true,
            'identificationBulkId' => 'Some bulk ID'
        ]);
        $this->card = $this->getValidCard();
        $this->card['email'] = 'email@example.com';
        $this->card['title'] = 'DR';
        $this->card['gender'] = 'M';
        $this->card['birthday'] = '1970-01-01';
        $this->card['company'] = 'Company name Inc.';
    }

    /**
     * @return CopyAndPayPurchaseResponse
     */
    public function testPurchase()
    {
        $this->setMockHttpResponse('CopyAndPayIntegratorGenerateTokenSuccess.txt');
        $options = $this->options;
        $options['returnUrl'] = 'https://nonexistent.example/some/return/url';
        $options['brands'] = 'VISA';
        $options['transactionId'] = 'Transaction 12345';
        $options['card'] = $this->card;
        $request = $this->gateway->purchase($options);
        $request->setPresentationUsage('Used for test');
        $response = $request->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseRequest', $request);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var \Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest $request */
        $this->assertSame('Shopper 13245', $request->getIdentificationShopperId());
        $this->assertSame('Transaction 12345', $request->getTransactionId());
        $this->assertSame('Used for test', $request->getPresentationUsage());
        $this->assertEmpty($request->getIdentificationBulkId());
        $this->assertEmpty($request->getIdentificationInvoiceId());
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $widget = $response->getWidget();
        $this->assertNotEmpty($widget);
        $this->assertStringEndsWith('>VISA</form>', (string) $widget);
        $this->assertStringEndsWith('>VISA</form>', $widget->renderHtmlForm());
        $this->assertStringStartsWith('<form action="https://nonexistent.example/some/return/url"', $widget->renderHtmlForm());
        $this->assertNotEmpty($response->getWidget());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());

        return $response;
    }

    /**
     * @depends testPurchase
     * @param CopyAndPayPurchaseResponse $purchaseResponse
     */
    public function testWaitingCompletePurchase(CopyAndPayPurchaseResponse $purchaseResponse)
    {
        $this->setMockHttpResponse('CopyAndPayIntegratorGetStatusWaitingForShopper.txt');

        $response = $this->gateway->completePurchase()->fill($purchaseResponse)->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseResponse', $response);
        /** @var CopyAndPayCompletePurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertTrue($response->isWaiting());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertEmpty($response->getIdentificationTransactionId());
        $this->assertEmpty($response->getIdentificationShopperId());
        $this->assertEmpty($response->getIdentificationUniqueId());
        $this->assertEmpty($response->getIdentificationShortId());
    }


    public function testConnectorModePurchase()
    {
        $this->setMockHttpResponse('CopyAndPayConnectorGenerateTokenSuccess.txt');

        $options = $this->options;
        $options['brands'] = ['MAESTRO', 'MASTER'];
        $options['paymentMemo'] = 'TEST MEMO';
        $options['returnUrl'] = 'https://nonexistent.example/return/url';

        $request = $this->connectorModeGateway->purchase($options);
        $request->setIdentificationInvoiceId(248);
        $response = $request->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseRequest', $request);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var \Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest $request */
        $this->assertEmpty($request->getIdentificationShopperId());
        $this->assertEmpty($request->getTransactionId());
        $this->assertSame('Some bulk ID', $request->getIdentificationBulkId());
        $this->assertSame(248, $request->getIdentificationInvoiceId());
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $widget = $response->getWidget();
        $this->assertNotEmpty($widget);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $this->assertStringEndsWith('>MAESTRO MASTER</form>', (string) $widget);
        $this->assertStringEndsWith('>MAESTRO MASTER</form>', $widget->renderHtmlForm());
        $this->assertStringStartsWith('<form action="https://nonexistent.example/return/other/url"', $widget->renderHtmlForm(['returnUrl' => 'https://nonexistent.example/return/other/url']));
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());

        return $response;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage brands
     */
    public function testEmptyBrandsPurchase()
    {
        $this->setMockHttpResponse('CopyAndPayIntegratorGenerateTokenSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $widget = $response->getWidget();
        $this->assertNotEmpty($widget);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $widget->render(['returnUrl' => 'https://nonexistent.example/return/url']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage returnUrl
     */
    public function testEmptyReturnUrlPurchase()
    {
        $this->setMockHttpResponse('CopyAndPayIntegratorGenerateTokenSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse', $response);
        /** @var CopyAndPayPurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertNotEmpty($response->getTransactionToken());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
        $widget = $response->getWidget();
        $this->assertNotEmpty($widget);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $widget->render(['brands' => ['VISA']]);
    }


    public function testInvalidTokenCompletePurchase()
    {
        $this->setMockHttpResponse('CopyAndPayIntegratorGetStatusInvalidTokenError.txt');

        $response = $this->gateway->completePurchase()->setTransactionToken('TEST_INVALID_TOKEN')->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseResponse', $response);
        /** @var CopyAndPayCompletePurchaseResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertFalse($response->isWaiting());
        $this->assertSame('Invalid or expired token', $response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());
    }


    public function testSuccessRegisteredCompletePurchase()
    {
        $this->setMockHttpResponse('CopyAndPayIntegratorGetStatusRegisteredSuccess.txt');
        $token = 'E60B057DF9CDADE6784DA3E5E285385D.sbg-vm-fe01';
        $httpRequest = new Request(['token' => $token]);
        $gateway = new COPYandPAYGateway($this->getHttpClient(), $httpRequest);
        $gateway->setTestMode(true);
        $request = $gateway->completePurchase();
        $response = $request->send();

        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseRequest', $request);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\CopyAndPayCompletePurchaseResponse', $response);
        /** @var $response CopyAndPayCompletePurchaseResponse */
        $this->assertFalse($response->isWaiting());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertNotEmpty($response->getCode());
        $this->assertSame('90', $response->getCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('8a82944a4cfff62d014d01522e25136a', $response->getTransactionReference());
        $this->assertSame('Optional identification of this transaction 123', $response->getIdentificationTransactionId());
        $this->assertSame('Optional identification of customer', $response->getIdentificationShopperId());
        $this->assertSame('8a82944a4cfff62d014d01522e25136a', $response->getIdentificationUniqueId());
        $this->assertSame('5871.8096.6562', $response->getIdentificationShortId());
        $this->assertSame($response->getIdentificationUniqueId(), $response->getTransactionReference());
        $this->assertSame($response->getIdentificationTransactionId(), $response->getTransactionId());
        $expectedCardReference = 'eyJhciI6IjhhODI5NDRhNGNmZmY2MmQwMTRkMDE1MjJjNTQxMTExIiwicGMiOiJDQy5EQiJ9';
        $this->assertSame($expectedCardReference, $response->getCardReference());
        $this->assertSame('SYNC', $response->getTransactionResponse());
        $this->assertNull($response->getPresentationAmount());
        $this->assertNull($response->getPresentationCurrency());
        $this->assertNull($response->getPresentationUsage());;
    }
}
