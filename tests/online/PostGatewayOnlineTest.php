<?php

namespace Omnipay\PayUnity;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\PostGateway;
use Omnipay\PayUnity\AccountRegistrationReference;

class PostGatewayOnlineTest extends GatewayTestCase
{
    public function setUp()
    {
        $logger = new Logger('UnitTest logger');
        $logger->pushHandler(new RotatingFileHandler(__DIR__.'/../../tmp/logs/unit-tests.log'));
        $accountRegistrationReference = getenv('PAYUNITY_DRIVER_FOR_OMNIPAY_TESTING_ACCOUNT_REGISTRATION_REFERENCE');
        $this->accountRegistrationReference = $accountRegistrationReference;
        $gateway = new PostGateway($this->getHttpClient(), $this->getHttpRequest());
        $gateway->setTestMode(true);
        $gateway->setSecuritySender(getenv('PAYUNITY_SECURITY_SENDER') ?: '696a8f0fabffea91517d0eb0a0bf9c33');
        $gateway->setTransactionChannel(getenv('PAYUNITY_TRANSACTION_CHANNEL') ?: '52275ebaf361f20a76b038ba4c806991');
        $gateway->setUserLogin(getenv('PAYUNITY_USER_LOGIN') ?: '1143238d620a572a726fe92eede0d1ab');
        $gateway->setUserPwd(getenv('PAYUNITY_USER_PWD') ?: 'demo');
        $gateway->setIdentificationShopperId('Test shopper');
        $gateway->attachPsrLogger($logger);
        $this->gateway = $gateway;
    }


    public function testPurchaseSuccess()
    {
        if (empty($this->accountRegistrationReference)) {
            $this->markTestSkipped('Account registration reference not set');
        }
        $request = $this->gateway->purchase();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostPurchaseRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostPurchaseRequest $request  */
        $request->setAmount('2.50');
        $request->setCurrency('EUR');
        $request->setCardReference($this->accountRegistrationReference);
        $description = 'SomePurchase'.uniqid();
        $transactionId = 'TEST_PURCHASE_'.uniqid();
        $invoiceId = 'Test purchase invoice ID'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test payment using token');
        $request->setDescription($description);
        $request->setIdentificationBulkId('Test purchase Bulk ID 123');
        $request->setIdentificationInvoiceId($invoiceId);
        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());

        $this->assertSame('90', $response->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());

        $transactionReference = $response->getTransactionReference();
        $this->assertNotEmpty($transactionReference);
        $this->assertSame($transactionReference, $response->getIdentificationUniqueId());
        $this->assertSame($transactionId, $response->getTransactionId());
        $this->assertSame($transactionId, $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertNotEmpty($response->getIdentificationShortId());
        $this->assertSame('2.50', $response->getPresentationAmount());
        $this->assertSame('EUR', $response->getPresentationCurrency());
        $this->assertSame($description, $response->getPresentationUsage());

        $this->assertSame('SYNC', $response->getTransactionResponse());
        $this->assertSame('00', $response->getProcessingReasonCode());
        $this->assertSame('CC.DB.90.00', $response->getProcessingCode());
        $this->assertSame('Successful Processing', $response->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getProcessingReturn());
        $this->assertSame('ACK', $response->getProcessingResult());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
        $this->assertSame('90', $response->getProcessingStatusCode());
        $this->assertSame('CC.DB', $response->getPaymentCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());
        $referenceContainer = AccountRegistrationReference::rebuild($this->accountRegistrationReference);
        $this->assertSame($referenceContainer->accountRegistration, $response->getAccountRegistration());

        return $transactionReference;
    }

    /**
     * @depends testPurchaseSuccess
     * @param string $transactionReference
     * @return null|string
     */
    public function testVoidSuccess($transactionReference)
    {
        $request = $this->gateway->void();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostVoidRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostVoidRequest $request  */
        $request->setTransactionReference($transactionReference);
        $request->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_VOID_'.uniqid();
        $invoiceId = 'Test void invoice ID'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test voiding');
        $request->setIdentificationBulkId('Test void Bulk ID 123');
        $request->setIdentificationInvoiceId($invoiceId);
        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());

        $newTransactionReference = $response->getTransactionReference();
        $this->assertNotEmpty($newTransactionReference);
        $this->assertNotSame($transactionReference, $newTransactionReference);
        $this->assertSame($newTransactionReference, $response->getIdentificationUniqueId());

        $this->assertSame('90', $response->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());

        $this->assertSame($transactionId, $response->getTransactionId());
        $this->assertSame($transactionId, $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertNotEmpty($response->getIdentificationShortId());
        $this->assertSame('0.00', $response->getPresentationAmount());
        $this->assertNull($response->getPresentationCurrency());

        $this->assertSame('SYNC', $response->getTransactionResponse());
        $this->assertSame('00', $response->getProcessingReasonCode());
        $this->assertSame('CC.RV.90.00', $response->getProcessingCode());
        $this->assertSame('Successful Processing', $response->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getProcessingReturn());
        $this->assertSame('ACK', $response->getProcessingResult());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
        $this->assertSame('90', $response->getProcessingStatusCode());
        $this->assertSame('CC.RV', $response->getPaymentCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());

        return $transactionReference;
    }

    /**
     * @depends testVoidSuccess
     * @param string $transactionReference
     */
    public function testVoidFailure($transactionReference)
    {
        $request = $this->gateway->void();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostVoidRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostVoidRequest $request  */
        $request->setTransactionReference($transactionReference);
        $request->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_VOID_'.uniqid();
        $invoiceId = 'Test void invoice ID'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test voiding');
        $request->setIdentificationBulkId('Test void Bulk ID 123');
        $request->setIdentificationInvoiceId($invoiceId);
        $request->setDescription('TestVoidFailure');
        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());

        $newTransactionReference = $response->getTransactionReference();
        $this->assertNotEmpty($newTransactionReference);
        $this->assertNotSame($transactionReference, $newTransactionReference);
        $this->assertSame($newTransactionReference, $response->getIdentificationUniqueId());

        $this->assertSame('70', $response->getCode());
        $this->assertSame("Reference Error : cannot reverse (already refunded|reversed or invalid workflow?)", $response->getMessage());

        $this->assertSame($transactionId, $response->getTransactionId());
        $this->assertSame($transactionId, $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertNotEmpty($response->getIdentificationShortId());
        $this->assertSame('0.00', $response->getPresentationAmount());
        $this->assertNull($response->getPresentationCurrency());
        $this->assertSame('TestVoidFailure', $response->getPresentationUsage());

        $this->assertSame('SYNC', $response->getTransactionResponse());
        $this->assertSame('30', $response->getProcessingReasonCode());
        $this->assertSame('CC.RV.70.30', $response->getProcessingCode());
        $this->assertSame('Reference Error', $response->getProcessingReason());
        $this->assertSame("cannot reverse (already refunded|reversed or invalid workflow?)", $response->getProcessingReturn());
        $this->assertSame('NOK', $response->getProcessingResult());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
        $this->assertSame('70', $response->getProcessingStatusCode());
        $this->assertSame('CC.RV', $response->getPaymentCode());
        $this->assertSame('700.400.300', $response->getProcessingReturnCode());
    }

    /**
     * @return null|string
     */
    public function testRefundSuccess()
    {
        if (empty($this->accountRegistrationReference)) {
            $this->markTestSkipped('Account registration reference not set');
        }
        $purchaseRequest = $this->gateway->purchase();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostPurchaseRequest', $purchaseRequest);
        /** @var \Omnipay\PayUnity\Message\PostPurchaseRequest $purchaseRequest  */
        $purchaseRequest->setAmount('1.10');
        $purchaseRequest->setCurrency('EUR');
        $purchaseRequest->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_PURCHASE_'.uniqid();
        $invoiceId = 'Test purchase invoice ID'.uniqid();
        $usage = 'SomePurchase'.uniqid();
        $purchaseRequest->setTransactionId($transactionId);
        $purchaseRequest->setPaymentMemo('Test payment using token');
        $purchaseRequest->setIdentificationBulkId('Test purchase Bulk ID 123');
        $purchaseRequest->setIdentificationInvoiceId($invoiceId);
        $purchaseRequest->setPresentationUsage($usage);
        $purchaseResponse = $purchaseRequest->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $purchaseResponse);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $purchaseResponse */
        $this->assertTrue($purchaseResponse->isSuccessful());
        $this->assertFalse($purchaseResponse->isWaiting());
        $this->assertFalse($purchaseResponse->isTransactionToken());
        $this->assertFalse($purchaseResponse->haveWidget());
        $this->assertFalse($purchaseResponse->isRedirect());
        $this->assertFalse($purchaseResponse->isTransparentRedirect());

        $this->assertSame('90', $purchaseResponse->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $purchaseResponse->getMessage());

        $transactionReference = $purchaseResponse->getTransactionReference();
        $this->assertNotEmpty($transactionReference);
        $this->assertSame($transactionReference, $purchaseResponse->getIdentificationUniqueId());
        $this->assertSame($transactionId, $purchaseResponse->getTransactionId());
        $this->assertSame($transactionId, $purchaseResponse->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $purchaseResponse->getIdentificationShopperId());
        $this->assertNotEmpty($purchaseResponse->getIdentificationShortId());
        $this->assertSame('1.10', $purchaseResponse->getPresentationAmount());
        $this->assertSame('EUR', $purchaseResponse->getPresentationCurrency());
        $this->assertSame($usage, $purchaseResponse->getPresentationUsage());

        $this->assertSame('SYNC', $purchaseResponse->getTransactionResponse());
        $this->assertSame('00', $purchaseResponse->getProcessingReasonCode());
        $this->assertSame('CC.DB.90.00', $purchaseResponse->getProcessingCode());
        $this->assertSame('Successful Processing', $purchaseResponse->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $purchaseResponse->getProcessingReturn());
        $this->assertSame('ACK', $purchaseResponse->getProcessingResult());
        $this->assertSame('ACK', $purchaseResponse->getPostValidationErrorCode());
        $this->assertSame('90', $purchaseResponse->getProcessingStatusCode());
        $this->assertSame('CC.DB', $purchaseResponse->getPaymentCode());
        $this->assertSame('000.100.110', $purchaseResponse->getProcessingReturnCode());
        $referenceContainer = AccountRegistrationReference::rebuild($this->accountRegistrationReference);
        $this->assertSame($referenceContainer->accountRegistration, $purchaseResponse->getAccountRegistration());

        $refundRequest1 = $this->gateway->refund();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostRefundRequest', $refundRequest1);
        /** @var \Omnipay\PayUnity\Message\PostRefundRequest $refundRequest1  */
        $this->assertSame($refundRequest1, $refundRequest1->fill($purchaseResponse));
        $this->assertSame($refundRequest1, $refundRequest1->setAmount('0.40'));
        $this->assertSame($refundRequest1, $refundRequest1->setIdentificationShopperId(null));
        $this->assertNull($refundRequest1->getTransactionId());
        $this->assertNull($refundRequest1->getIdentificationTransactionId());
        $this->assertNull($refundRequest1->getIdentificationShopperId());
        $this->assertNotContains('Test shopper', $refundRequest1->getData());

        $refundResponse1 = $refundRequest1->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $refundResponse1);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $refundResponse1 */
        $this->assertTrue($refundResponse1->isSuccessful());
        $this->assertFalse($refundResponse1->isWaiting());
        $this->assertFalse($refundResponse1->isTransactionToken());
        $this->assertFalse($refundResponse1->haveWidget());
        $this->assertFalse($refundResponse1->isRedirect());
        $this->assertFalse($refundResponse1->isTransparentRedirect());

        $newTransactionReference1 = $refundResponse1->getTransactionReference();
        $this->assertNotEmpty($newTransactionReference1);
        $this->assertNotSame($transactionReference, $newTransactionReference1);
        $this->assertSame($newTransactionReference1, $refundResponse1->getIdentificationUniqueId());

        $this->assertSame('90', $refundResponse1->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $refundResponse1->getMessage());

        $this->assertNotEmpty($transactionId);
        $this->assertSame($transactionId, $refundResponse1->getTransactionId());
        $this->assertSame($transactionId, $refundResponse1->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $refundResponse1->getIdentificationShopperId());
        $this->assertNotEmpty($refundResponse1->getIdentificationShortId());
        $this->assertSame('0.40', $refundResponse1->getPresentationAmount());
        $this->assertSame('EUR', $refundResponse1->getPresentationCurrency());
        $this->assertSame($usage, $refundResponse1->getPresentationUsage());

        $this->assertSame('SYNC', $refundResponse1->getTransactionResponse());
        $this->assertSame('00', $refundResponse1->getProcessingReasonCode());
        $this->assertSame('CC.RF.90.00', $refundResponse1->getProcessingCode());
        $this->assertSame('Successful Processing', $refundResponse1->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $refundResponse1->getProcessingReturn());
        $this->assertSame('ACK', $refundResponse1->getProcessingResult());
        $this->assertSame('ACK', $refundResponse1->getPostValidationErrorCode());
        $this->assertSame('90', $refundResponse1->getProcessingStatusCode());
        $this->assertSame('CC.RF', $refundResponse1->getPaymentCode());
        $this->assertSame('000.100.110', $refundResponse1->getProcessingReturnCode());

        $refundRequest2 = $this->gateway->refund();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostRefundRequest', $refundRequest2);
        /** @var \Omnipay\PayUnity\Message\PostRefundRequest $refundRequest2  */
        $refundRequest2->setTransactionReference($transactionReference);
        $refundRequest2->setCardReference($this->accountRegistrationReference);
        $refundRequest2->setAmount('0.50');
        $refundRequest2->setCurrency('EUR');
        $refund2TransactionId = 'TEST_REFUND_2_'.uniqid();
        $refund2InvoiceId = 'Test refund invoice ID'.uniqid();
        $refundRequest2->setTransactionId($refund2TransactionId);
        $refundRequest2->setPaymentMemo('Test refunding');
        $refundRequest2->setIdentificationBulkId('Test refund Bulk ID 123');
        $refundRequest2->setIdentificationInvoiceId($refund2InvoiceId);

        $refundResponse2 = $refundRequest2->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $refundResponse2);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $refundResponse2 */
        $this->assertTrue($refundResponse2->isSuccessful());
        $this->assertFalse($refundResponse2->isWaiting());
        $this->assertFalse($refundResponse2->isTransactionToken());
        $this->assertFalse($refundResponse2->haveWidget());
        $this->assertFalse($refundResponse2->isRedirect());
        $this->assertFalse($refundResponse2->isTransparentRedirect());

        $newTransactionReference2 = $refundResponse2->getTransactionReference();
        $this->assertNotEmpty($newTransactionReference2);
        $this->assertNotSame($transactionReference, $newTransactionReference2);
        $this->assertSame($newTransactionReference2, $refundResponse2->getIdentificationUniqueId());

        $this->assertSame('90', $refundResponse2->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $refundResponse2->getMessage());

        $this->assertSame($refund2TransactionId, $refundResponse2->getTransactionId());
        $this->assertSame($refund2TransactionId, $refundResponse2->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $refundResponse2->getIdentificationShopperId());
        $this->assertNotEmpty($refundResponse2->getIdentificationShortId());
        $this->assertSame('0.50', $refundResponse2->getPresentationAmount());
        $this->assertSame('EUR', $refundResponse2->getPresentationCurrency());
        $this->assertNull($refundResponse2->getPresentationUsage());

        $this->assertSame('SYNC', $refundResponse2->getTransactionResponse());
        $this->assertSame('00', $refundResponse2->getProcessingReasonCode());
        $this->assertSame('CC.RF.90.00', $refundResponse2->getProcessingCode());
        $this->assertSame('Successful Processing', $refundResponse2->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $refundResponse2->getProcessingReturn());
        $this->assertSame('ACK', $refundResponse2->getProcessingResult());
        $this->assertSame('ACK', $refundResponse2->getPostValidationErrorCode());
        $this->assertSame('90', $refundResponse2->getProcessingStatusCode());
        $this->assertSame('CC.RF', $refundResponse2->getPaymentCode());
        $this->assertSame('000.100.110', $refundResponse2->getProcessingReturnCode());

        return $transactionReference;
    }


    /**
     * @depends testRefundSuccess
     * @param string $transactionReference
     */
    public function testRefundFailure($transactionReference)
    {
        $refundRequest3 = $this->gateway->refund();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostRefundRequest', $refundRequest3);
        /** @var \Omnipay\PayUnity\Message\PostRefundRequest $refundRequest3  */
        $refundRequest3->setTransactionReference($transactionReference);
        $refundRequest3->setCardReference($this->accountRegistrationReference);
        $refundRequest3->setAmount('0.30');
        $refundRequest3->setCurrency('EUR');
        $transactionId = 'TEST_REFUND_3_'.uniqid();
        $invoiceId = 'Test refund invoice ID'.uniqid();
        $usage3 = 'SomeRefundFailure'.uniqid();
        $refundRequest3->setTransactionId($transactionId);
        $refundRequest3->setPaymentMemo('Test refunding');
        $refundRequest3->setIdentificationBulkId('Test refund Bulk ID 123');
        $refundRequest3->setIdentificationInvoiceId($invoiceId);
        $refundRequest3->setPresentationUsage($usage3);

        $refundResponse3 = $refundRequest3->send();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $refundResponse3);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $refundResponse3 */
        $this->assertFalse($refundResponse3->isSuccessful());
        $this->assertFalse($refundResponse3->isWaiting());
        $this->assertFalse($refundResponse3->isTransactionToken());
        $this->assertFalse($refundResponse3->haveWidget());
        $this->assertFalse($refundResponse3->isRedirect());
        $this->assertFalse($refundResponse3->isTransparentRedirect());

        $newTransactionReference = $refundResponse3->getTransactionReference();
        $this->assertNotEmpty($newTransactionReference);
        $this->assertNotSame($transactionReference, $newTransactionReference);
        $this->assertSame($newTransactionReference, $refundResponse3->getIdentificationUniqueId());

        $this->assertSame('70', $refundResponse3->getCode());
        $this->assertSame("Reference Error : cannot refund (refund volume exceeded or tx reversed or invalid workflow?)", $refundResponse3->getMessage());

        $this->assertSame($transactionId, $refundResponse3->getTransactionId());
        $this->assertSame($transactionId, $refundResponse3->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $refundResponse3->getIdentificationShopperId());
        $this->assertNotEmpty($refundResponse3->getIdentificationShortId());
        $this->assertSame('0.30', $refundResponse3->getPresentationAmount());
        $this->assertSame('EUR', $refundResponse3->getPresentationCurrency());
        $this->assertSame($usage3, $refundResponse3->getPresentationUsage());

        $this->assertSame('SYNC', $refundResponse3->getTransactionResponse());
        $this->assertSame('30', $refundResponse3->getProcessingReasonCode());
        $this->assertSame('CC.RF.70.30', $refundResponse3->getProcessingCode());
        $this->assertSame('Reference Error', $refundResponse3->getProcessingReason());
        $this->assertSame("cannot refund (refund volume exceeded or tx reversed or invalid workflow?)", $refundResponse3->getProcessingReturn());
        $this->assertSame('NOK', $refundResponse3->getProcessingResult());
        $this->assertSame('ACK', $refundResponse3->getPostValidationErrorCode());
        $this->assertSame('70', $refundResponse3->getProcessingStatusCode());
        $this->assertSame('CC.RF', $refundResponse3->getPaymentCode());
        $this->assertSame('700.400.200', $refundResponse3->getProcessingReturnCode());
    }

    /**
     * @return Message\GenericPostResponse
     */
    public function testAuthorizeSuccess()
    {
        if (empty($this->accountRegistrationReference)) {
            $this->markTestSkipped('Account registration reference not set');
        }
        $request = $this->gateway->authorize();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostAuthorizeRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostAuthorizeRequest $request  */
        $request->setAmount('1.20');
        $request->setCurrency('EUR');
        $request->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_AUTHORIZE_'.uniqid();
        $invoiceId = 'Test authorize invoice ID'.uniqid();
        $usage = 'SomeAuthorize'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test authorization using token');
        $request->setIdentificationBulkId('Test authorize Bulk ID 123');
        $request->setIdentificationInvoiceId($invoiceId);
        $request->setPresentationUsage($usage);

        $response = $request->send();

        $this->checkSuccessfulResponse($response, $request);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertSame('CC.PA.90.00', $response->getProcessingCode());
        $this->assertSame('CC.PA', $response->getPaymentCode());
        
        $referenceContainer = AccountRegistrationReference::rebuild($this->accountRegistrationReference);
        $this->assertSame($referenceContainer->accountRegistration, $response->getAccountRegistration());

        return $response;
    }

    /**
     * @depends testAuthorizeSuccess
     * @param \Omnipay\PayUnity\Message\GenericPostResponse $response
     * @return null|string
     */
    public function testCaptureSuccess($response)
    {
        $this->checkCaptureSuccess($response);
        $this->checkCaptureSuccess($response);
        return $response;
    }

    /**
     * @depends testCaptureSuccess
     * @param \Omnipay\PayUnity\Message\GenericPostResponse $response
     */
    public function testCaptureFailure($response)
    {
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        $request = $this->gateway->capture();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostCaptureRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostCaptureRequest $request  */
        $request->fill($response);
        $request->setAmount('0.50');
        $request->setCurrency('EUR');
        $transactionId = 'TEST_CAPTURE_FAILURE_'.uniqid();
        $invoiceId = 'Test capture failure invoice ID'.uniqid();
        $usage = 'SomeCaptureFailure'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test capturing');
        $request->setIdentificationBulkId('Test capture Bulk ID 123');
        $request->setIdentificationInvoiceId($invoiceId);
        $request->setPresentationUsage($usage);

        $response = $request->send();

        $this->checkGenericPostResponse($response, $request);

        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertFalse($response->isSuccessful());

        $this->assertSame('70', $response->getCode());
        $this->assertSame("Reference Error : cannot capture (PA value exceeded, PA reverted or invalid workflow?)", $response->getMessage());

        $this->assertSame('30', $response->getProcessingReasonCode());
        $this->assertSame('CC.CP.70.30', $response->getProcessingCode());
        $this->assertSame('Reference Error', $response->getProcessingReason());
        $this->assertSame("cannot capture (PA value exceeded, PA reverted or invalid workflow?)", $response->getProcessingReturn());
        $this->assertSame('NOK', $response->getProcessingResult());
        $this->assertSame('70', $response->getProcessingStatusCode());
        $this->assertSame('CC.CP', $response->getPaymentCode());
        $this->assertSame('700.400.100', $response->getProcessingReturnCode());
    }

    /**
     * @param \Omnipay\PayUnity\Message\GenericPostResponse $response
     */
    protected function checkCaptureSuccess($response)
    {
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        $request = $this->gateway->capture();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostCaptureRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostCaptureRequest $request  */
        $request->fill($response);
        $request->setAmount('0.50');
        $request->setCurrency('EUR');
        $transactionId = 'TEST_CAPTURE_'.uniqid();
        $invoiceId = 'Test capture invoice ID'.uniqid();
        $usage = 'SomeCapture'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test capturing');
        $request->setIdentificationBulkId('Test capture Bulk ID 123');
        $request->setIdentificationInvoiceId($invoiceId);
        $request->setPresentationUsage($usage);

        $response = $request->send();

        $this->checkSuccessfulResponse($response, $request);

        $this->assertSame('CC.CP.90.00', $response->getProcessingCode());
        $this->assertSame('CC.CP', $response->getPaymentCode());
    }


    protected function checkSuccessfulResponse($response, $request)
    {
        $this->checkGenericPostResponse($response, $request);

        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertTrue($response->isSuccessful());

        $this->assertSame('90', $response->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());
        $this->assertSame('90', $response->getProcessingStatusCode());
        $this->assertSame('00', $response->getProcessingReasonCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());
        $this->assertSame('ACK', $response->getProcessingResult());
        $this->assertSame('Successful Processing', $response->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getProcessingReturn());
    }


    protected function checkGenericPostResponse($response, $request)
    {
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostRequest', $request);
        /** @var \Omnipay\PayUnity\Message\GenericPostRequest $request */
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\GenericPostResponse', $response);
        /** @var  \Omnipay\PayUnity\Message\GenericPostResponse $response */
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertNull($response->getWidget());

        $newTransactionReference = $response->getTransactionReference();
        $this->assertNotEmpty($newTransactionReference);
        $this->assertNotSame($request->getTransactionReference(), $newTransactionReference);
        $this->assertSame($newTransactionReference, $response->getIdentificationUniqueId());

        $this->assertSame($request->getTransactionId(), $response->getTransactionId());
        $this->assertSame($response->getTransactionId(), $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertNotEmpty($response->getIdentificationShortId());
        $expectedAmount = $request->getAmount() ?: '0.00';
        $this->assertSame($expectedAmount, $response->getPresentationAmount());
        $this->assertSame($request->getCurrency(), $response->getPresentationCurrency());
        $this->assertSame($request->getDescription(), $response->getPresentationUsage());

        $this->assertSame('SYNC', $response->getTransactionResponse());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
    }
}
