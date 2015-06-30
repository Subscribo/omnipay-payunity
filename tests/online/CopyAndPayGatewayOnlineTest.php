<?php

namespace Omnipay\PayUnity;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\COPYandPAYGateway;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;

/**
 * Class CopyAndPayGatewayOnlineTest
 *
 * Testing class actually connecting to remote API
 *
 */
class CopyAndPayGatewayOnlineTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new COPYandPAYGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->logger = new Logger('UnitTest logger');
        $this->logger->pushHandler(new RotatingFileHandler(__DIR__.'/../../tmp/logs/unit-tests.log'));
        $this->gateway->attachPsrLogger($this->logger);

        $this->gateway->setSecuritySender(getenv('PAYUNITY_SECURITY_SENDER') ?: '696a8f0fabffea91517d0eb0a0bf9c33');
        $this->gateway->setTransactionChannel(getenv('PAYUNITY_TRANSACTION_CHANNEL') ?: '52275ebaf361f20a76b038ba4c806991');
        $this->gateway->setUserLogin(getenv('PAYUNITY_USER_LOGIN') ?: '1143238d620a572a726fe92eede0d1ab');
        $this->gateway->setUserPwd(getenv('PAYUNITY_USER_PWD') ?: 'demo');
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
        $options = $this->options;
        $options['returnUrl'] = 'https://nonexistent.example/return/url';
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
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $this->assertStringEndsWith('>VISA</form>', (string) $widget);
        $this->assertStringEndsWith('>VISA</form>', $widget->renderHtmlForm());
        $this->assertStringStartsWith('<form action="https://nonexistent.example/return/url"', $widget->renderHtmlForm());
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
        $options = $this->options;
        $options['brands'] = ['MAESTRO', 'MASTER'];
        $options['paymentMemo'] = 'TEST MEMO';

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
        $widget->setReturnUrl('https://nonexistent.example/return/url');
        $this->assertNotEmpty($widget);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $this->assertStringEndsWith('>MAESTRO MASTER</form>', (string) $widget);
        $this->assertStringEndsWith('>MAESTRO MASTER</form>', $widget->renderHtmlForm());
        $this->assertStringStartsWith('<form action="https://nonexistent.example/return/url"', $widget->renderHtmlForm());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getTransactionReference());

        return $response;
    }


    /**
     * @depends testConnectorModePurchase
     * @param CopyAndPayPurchaseResponse $purchaseResponse
     */
    public function testConnectorModeWaitingCompletePurchase(CopyAndPayPurchaseResponse $purchaseResponse)
    {
        $response = $this->connectorModeGateway->completePurchase()->fill($purchaseResponse)->send();
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


    /**
     * @expectedException \InvalidArgumentException
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage brands
     */
    public function testEmptyBrandsPurchase()
    {
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
        $widget->render(['brands' => 'VISA MAESTRO']);
    }

    public function testInvalidTokenCompletePurchase()
    {
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
}
