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
        $transactionId = 'TEST_PURCHASE_'.uniqid();
        $invoiceId = 'Test purchase invoice ID'.uniqid();
        $request->setTransactionId($transactionId);
        $request->setPaymentMemo('Test payment using token');
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
     * @return string
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
        $this->assertSame('30', $response->getProcessingReasonCode());
        $this->assertSame('CC.RV.70.30', $response->getProcessingCode());
        $this->assertSame('Reference Error', $response->getProcessingReason());
        $this->assertSame("cannot reverse (already refunded|reversed or invalid workflow?)", $response->getProcessingReturn());
        $this->assertSame('NOK', $response->getProcessingResult());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
        $this->assertSame('70', $response->getProcessingStatusCode());
        $this->assertSame('CC.RV', $response->getPaymentCode());
        $this->assertSame('700.400.300', $response->getProcessingReturnCode());

        return $transactionReference;
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
        $purchaseRequest->setTransactionId($transactionId);
        $purchaseRequest->setPaymentMemo('Test payment using token');
        $purchaseRequest->setIdentificationBulkId('Test purchase Bulk ID 123');
        $purchaseRequest->setIdentificationInvoiceId($invoiceId);
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
        $refundRequest1->setTransactionReference($transactionReference);
        $refundRequest1->setCardReference($this->accountRegistrationReference);
        $refundRequest1->setAmount('0.40');
        $refundRequest1->setCurrency('EUR');
        $refund1TransactionId = 'TEST_REFUND_1_'.uniqid();
        $refund1InvoiceId = 'Test refund invoice ID'.uniqid();
        $refundRequest1->setTransactionId($refund1TransactionId);
        $refundRequest1->setPaymentMemo('Test refunding');
        $refundRequest1->setIdentificationBulkId('Test refund Bulk ID 123');
        $refundRequest1->setIdentificationInvoiceId($refund1InvoiceId);
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

        $this->assertSame($refund1TransactionId, $refundResponse1->getTransactionId());
        $this->assertSame($refund1TransactionId, $refundResponse1->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $refundResponse1->getIdentificationShopperId());
        $this->assertNotEmpty($refundResponse1->getIdentificationShortId());
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
        $refundRequest3->setTransactionId($transactionId);
        $refundRequest3->setPaymentMemo('Test refunding');
        $refundRequest3->setIdentificationBulkId('Test refund Bulk ID 123');
        $refundRequest3->setIdentificationInvoiceId($invoiceId);
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
}
