<?php

namespace Omnipay\PayUnity;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\PostGateway;
use Omnipay\PayUnity\AccountRegistrationReference;

class PostGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        $this->accountRegistrationReference = 'eyJhciI6InRlc3RyZWZlcmVuY2UiLCJwYyI6IkNDLkRCIn0=';
        $gateway = new PostGateway($this->getHttpClient(), $this->getHttpRequest());
        $gateway->setTestMode(true);
        $gateway->setSecuritySender('696a8f0fabffea91517d0eb0a0bf9c33');
        $gateway->setTransactionChannel('52275ebaf361f20a76b038ba4c806991');
        $gateway->setUserLogin('1143238d620a572a726fe92eede0d1ab');
        $gateway->setUserPwd('demo');
        $gateway->setIdentificationShopperId('Test shopper');
        $this->gateway = $gateway;
    }


    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PostPurchaseSuccess.txt');

        $request = $this->gateway->purchase();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostPurchaseRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostPurchaseRequest $request  */
        $request->setAmount('2.50');
        $request->setCurrency('EUR');
        $request->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_PURCHASE_ID';
        $invoiceId = 'Some invoice ID';
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

        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_1', $response->getTransactionReference());
        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_1', $response->getIdentificationUniqueId());
        $this->assertSame($transactionId, $response->getTransactionId());
        $this->assertSame($transactionId, $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertSame('some.short.id', $response->getIdentificationShortId());
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
    }


    public function testVoidSuccess()
    {
        $this->setMockHttpResponse('PostVoidSuccess.txt');

        $request = $this->gateway->void();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostVoidRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostVoidRequest $request  */
        $request->setTransactionReference('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_1');
        $request->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_VOID_ID';
        $invoiceId = 'Test void invoice ID';
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

        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_2', $response->getTransactionReference());
        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_2', $response->getIdentificationUniqueId());

        $this->assertSame('90', $response->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());

        $this->assertSame($transactionId, $response->getTransactionId());
        $this->assertSame($transactionId, $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertSame('some.short.id2', $response->getIdentificationShortId());
        $this->assertSame('00', $response->getProcessingReasonCode());
        $this->assertSame('CC.RV.90.00', $response->getProcessingCode());
        $this->assertSame('Successful Processing', $response->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getProcessingReturn());
        $this->assertSame('ACK', $response->getProcessingResult());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
        $this->assertSame('90', $response->getProcessingStatusCode());
        $this->assertSame('CC.RV', $response->getPaymentCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());
    }


    public function testVoidFailure()
    {
        $this->setMockHttpResponse('PostVoidFailure.txt');

        $request = $this->gateway->void();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostVoidRequest', $request);
        /** @var \Omnipay\PayUnity\Message\PostVoidRequest $request  */
        $request->setTransactionReference('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_1');
        $request->setCardReference($this->accountRegistrationReference);
        $transactionId = 'TEST_VOID_FAILURE_ID';
        $invoiceId = 'Test void failure invoice ID';
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

        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_3', $response->getTransactionReference());
        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_3', $response->getIdentificationUniqueId());

        $this->assertSame('70', $response->getCode());
        $this->assertSame("Reference Error : cannot reverse (already refunded|reversed or invalid workflow?)", $response->getMessage());

        $this->assertSame($transactionId, $response->getTransactionId());
        $this->assertSame($transactionId, $response->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertSame('some.short.id3', $response->getIdentificationShortId());
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


    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('PostRefundSuccess.txt');

        $refundRequest1 = $this->gateway->refund();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostRefundRequest', $refundRequest1);
        /** @var \Omnipay\PayUnity\Message\PostRefundRequest $refundRequest1  */
        $refundRequest1->setTransactionReference('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_1');
        $refundRequest1->setCardReference($this->accountRegistrationReference);
        $refundRequest1->setAmount('0.40');
        $refundRequest1->setCurrency('EUR');
        $refund1TransactionId = 'TEST_REFUND_1_ID';
        $refund1InvoiceId = 'Test refund invoice ID';
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

        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_4', $refundResponse1->getTransactionReference());
        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_4', $refundResponse1->getIdentificationUniqueId());

        $this->assertSame('90', $refundResponse1->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $refundResponse1->getMessage());

        $this->assertSame($refund1TransactionId, $refundResponse1->getTransactionId());
        $this->assertSame($refund1TransactionId, $refundResponse1->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $refundResponse1->getIdentificationShopperId());
        $this->assertSame('some.short.id4', $refundResponse1->getIdentificationShortId());
        $this->assertSame('00', $refundResponse1->getProcessingReasonCode());
        $this->assertSame('CC.RF.90.00', $refundResponse1->getProcessingCode());
        $this->assertSame('Successful Processing', $refundResponse1->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $refundResponse1->getProcessingReturn());
        $this->assertSame('ACK', $refundResponse1->getProcessingResult());
        $this->assertSame('ACK', $refundResponse1->getPostValidationErrorCode());
        $this->assertSame('90', $refundResponse1->getProcessingStatusCode());
        $this->assertSame('CC.RF', $refundResponse1->getPaymentCode());
        $this->assertSame('000.100.110', $refundResponse1->getProcessingReturnCode());
    }


    public function testRefundFailure()
    {
        $this->setMockHttpResponse('PostRefundFailure.txt');

        $refundRequest3 = $this->gateway->refund();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Message\\PostRefundRequest', $refundRequest3);
        /** @var \Omnipay\PayUnity\Message\PostRefundRequest $refundRequest3  */
        $refundRequest3->setTransactionReference('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_1');
        $refundRequest3->setCardReference($this->accountRegistrationReference);
        $refundRequest3->setAmount('0.30');
        $refundRequest3->setCurrency('EUR');
        $transactionId = 'TEST_REFUND_3_ID';
        $invoiceId = 'Test refund invoice ID';
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

        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_5', $refundResponse3->getTransactionReference());
        $this->assertSame('SOME_TRANSACTION_IDENTIFICATION_UNIQUE_ID_5', $refundResponse3->getIdentificationUniqueId());

        $this->assertSame('70', $refundResponse3->getCode());
        $this->assertSame("Reference Error : cannot refund (refund volume exceeded or tx reversed or invalid workflow?)", $refundResponse3->getMessage());

        $this->assertSame($transactionId, $refundResponse3->getTransactionId());
        $this->assertSame($transactionId, $refundResponse3->getIdentificationTransactionId());
        $this->assertSame('Test shopper', $refundResponse3->getIdentificationShopperId());
        $this->assertSame('some.short.id5', $refundResponse3->getIdentificationShortId());
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
