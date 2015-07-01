<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\GenericPostResponse;

class GenericPostResponseTest extends TestCase
{
    public function setUp()
    {
        $this->dataVoidOk = [
                        'P3_VALIDATION' => 'ACK',
                        'IDENTIFICATION_SHOPPERID' => 'Test shopper',
                        'CLEARING_DESCRIPTOR' => 'some clearing descriptor',
                        'TRANSACTION_CHANNEL' => 'some transaction channel',
                        'PROCESSING_REASON_CODE' => '00',
                        'PROCESSING_CODE' => 'CC.RV.90.00',
                        'FRONTEND_REQUEST_CANCELLED' => 'false',
                        'PROCESSING_REASON' => 'Successful Processing',
                        'FRONTEND_MODE' => 'DEFAULT',
                        'CLEARING_FXSOURCE' => 'INTERN',
                        'CLEARING_AMOUNT' => '2.50',
                        'PROCESSING_RESULT' => 'ACK',
                        'NAME_SALUTATION' => 'NONE',
                        'IDENTIFICATION_INVOICEID' => 'Some invoice ID',
                        'POST_VALIDATION' => 'ACK',
                        'CLEARING_CURRENCY' => 'EUR',
                        'FRONTEND_SESSION_ID' => '',
                        'PROCESSING_STATUS_CODE' => '90',
                        'PAYMENT_CODE' => 'CC.RV',
                        'PROCESSING_RETURN_CODE' => '000.100.110',
                        'CONTACT_IP' => '192.0.2.1',
                        'IDENTIFICATION_REFERENCEID' => 'some identification reference ID',
                        'PROCESSING_STATUS' => 'NEW',
                        'SECURITY_HASH' => 'some security hash',
                        'PRESENTATION_AMOUNT' => '0.00',
                        'FRONTEND_CC_LOGO' => 'link to image',
                        'IDENTIFICATION_UNIQUEID' => 'some identification unique ID',
                        'IDENTIFICATION_TRANSACTIONID' => 'some identification transaction ID',
                        'IDENTIFICATION_SHORTID' => 'some identification short ID',
                        'CLEARING_FXRATE' => '1.0',
                        'PROCESSING_TIMESTAMP' => '2015-06-30 15:31:43',
                        'PAYMENT_MEMO' => 'Some memo',
                        'ADDRESS_COUNTRY' => 'DE',
                        'RESPONSE_VERSION' => '1.0',
                        'TRANSACTION_MODE' => 'INTEGRATOR_TEST',
                        'TRANSACTION_RESPONSE' => 'SYNC',
                        'PROCESSING_RETURN' => "Request successfully processed in 'Merchant in Integrator Test Mode'",
                        'CLEARING_FXDATE' => '2015-06-30 15:31:43',
        ];
    }


    public function testVoidSuccessResponse()
    {
        $response = new GenericPostResponse($this->getMockRequest(), $this->dataVoidOk, 200);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidget());

        $this->assertSame('90', $response->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());
        $this->assertNull($response->getCardReference());
        $this->assertSame('some identification unique ID', $response->getTransactionReference());
        $this->assertSame('some identification unique ID', $response->getIdentificationUniqueId());
        $this->assertSame('some identification transaction ID', $response->getTransactionId());
        $this->assertSame('some identification transaction ID', $response->getIdentificationTransactionId());
        $this->assertNull($response->getAccountRegistration());
        $this->assertSame('some identification short ID', $response->getIdentificationShortId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertSame('Successful Processing', $response->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getProcessingReturn());
        $this->assertSame('ACK', $response->getProcessingResult());
        $this->assertSame('CC.RV.90.00', $response->getProcessingCode());
        $this->assertSame('00', $response->getProcessingReasonCode());
        $this->assertSame('90', $response->getProcessingStatusCode());
        $this->assertSame('90', $response->acquireProcessingStatusCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());
        $this->assertSame('CC.RV', $response->getPaymentCode());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
    }
    
    
    public function testPurchaseWithoutRegistrationTokenResponse()
    {
        $data = [
                    'TRANSACTION_CHANNEL'  => 'some transaction channel',
                    'PRESENTATION_CURRENCY'  => 'EUR',
                    'IDENTIFICATION_UNIQUEID'  => 'some identification unique ID',
                    'PAYMENT_CODE'  => 'CC.DB',
                    'FRONTEND_CC_LOGO'  => 'link to image',
                    'PROCESSING_STATUS'  => 'REJECTED_VALIDATION',
                    'CONTACT_IP'  => '192.0.2.1',
                    'FRONTEND_MODE'  => 'DEFAULT',
                    'FRONTEND_REQUEST_CANCELLED'  => 'false',
                    'PROCESSING_RETURN'  => 'request contains no creditcard, bank account number or bank name',
                    'PROCESSING_REASON'  => 'Account Validation',
                    'PROCESSING_STATUS_CODE'  => '70',
                    'SECURITY_HASH'  => 'some security hash',
                    'TRANSACTION_MODE'  => 'INTEGRATOR_TEST',
                    'POST_VALIDATION'  => 'ACK',
                    'PROCESSING_TIMESTAMP'  => '2015-07-01 12:06:13',
                    'PROCESSING_RETURN_CODE'  => '100.100.100',
                    'RESPONSE_VERSION'  => '1.0',
                    'TRANSACTION_RESPONSE'  => 'SYNC',
                    'P3_VALIDATION'  => 'ACK',
                    'PROCESSING_CODE'  => 'CC.DB.70.40',
                    'FRONTEND_SESSION_ID'  => '',
                    'PROCESSING_REASON_CODE'  => '40',
                    'IDENTIFICATION_SHORTID'  => 'some identification short ID',
                    'NAME_SALUTATION'  => 'NONE',
                    'PROCESSING_RESULT'  => 'NOK',
                    'PRESENTATION_AMOUNT'  => '1.05',
                    'ADDRESS_COUNTRY'  => 'DE',
        ];
        $response = new GenericPostResponse($this->getMockRequest(), $data, 200);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidget());

        $this->assertSame('70', $response->getCode());
        $this->assertSame("Account Validation : request contains no creditcard, bank account number or bank name", $response->getMessage());
        $this->assertNull($response->getCardReference());
        $this->assertSame('some identification unique ID', $response->getTransactionReference());
        $this->assertSame('some identification unique ID', $response->getIdentificationUniqueId());
        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getIdentificationTransactionId());
        $this->assertNull($response->getAccountRegistration());
        $this->assertSame('some identification short ID', $response->getIdentificationShortId());
        $this->assertNull($response->getIdentificationShopperId());
        $this->assertSame('Account Validation', $response->getProcessingReason());
        $this->assertSame("request contains no creditcard, bank account number or bank name", $response->getProcessingReturn());
        $this->assertSame('NOK', $response->getProcessingResult());
        $this->assertSame('CC.DB.70.40', $response->getProcessingCode());
        $this->assertSame('40', $response->getProcessingReasonCode());
        $this->assertSame('70', $response->getProcessingStatusCode());
        $this->assertSame('70', $response->acquireProcessingStatusCode());
        $this->assertSame('100.100.100', $response->getProcessingReturnCode());
        $this->assertSame('CC.DB', $response->getPaymentCode());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
    }
    
    
    public function testPurchaseSuccessResponse()
    {
        $data = [
                    'PROCESSING_RISK_SCORE' => '0',
                    'P3_VALIDATION' => 'ACK',
                    'IDENTIFICATION_SHOPPERID' => 'Test shopper',
                    'CLEARING_DESCRIPTOR' => 'some clearing descriptor',
                    'TRANSACTION_CHANNEL' => 'some transaction channel',
                    'PROCESSING_REASON_CODE' => '00',
                    'PROCESSING_CODE' => 'CC.DB.90.00',
                    'FRONTEND_REQUEST_CANCELLED' => 'false',
                    'PROCESSING_REASON' => 'Successful Processing',
                    'FRONTEND_MODE' => 'DEFAULT',
                    'CLEARING_FXSOURCE' => 'INTERN',
                    'CLEARING_AMOUNT' => '1.10',
                    'PROCESSING_RESULT' => 'ACK',
                    'NAME_SALUTATION' => 'NONE',
                    'IDENTIFICATION_INVOICEID' => 'Some invoice ID',
                    'POST_VALIDATION' => 'ACK',
                    'CLEARING_CURRENCY' => 'EUR',
                    'FRONTEND_SESSION_ID' => '',
                    'PROCESSING_STATUS_CODE' => '90',
                    'PRESENTATION_CURRENCY' => 'EUR',
                    'PAYMENT_CODE' => 'CC.DB',
                    'PROCESSING_RETURN_CODE' => '000.100.110',
                    'CONTACT_IP'  => '192.0.2.1',
                    'PROCESSING_STATUS' => 'NEW',
                    'SECURITY_HASH' => 'some security hash',
                    'FRONTEND_CC_LOGO' => 'link to image',
                    'PRESENTATION_AMOUNT' => '1.10',
                    'IDENTIFICATION_UNIQUEID' => 'some identification unique ID',
                    'IDENTIFICATION_TRANSACTIONID' => 'some identification transaction ID',
                    'IDENTIFICATION_SHORTID' =>  'some identification short ID',
                    'CLEARING_FXRATE' => '1.0',
                    'ACCOUNT_REGISTRATION' => 'someaccountregistration',
                    'PROCESSING_TIMESTAMP' => '2015-06-30 15:31:45',
                    'PAYMENT_MEMO' => 'Test payment using token',
                    'ADDRESS_COUNTRY' => 'DE',
                    'RESPONSE_VERSION' => '1.0',
                    'TRANSACTION_MODE' => 'INTEGRATOR_TEST',
                    'TRANSACTION_RESPONSE' => 'SYNC',
                    'PROCESSING_RETURN' => "Request successfully processed in 'Merchant in Integrator Test Mode'",
                    'CLEARING_FXDATE' => '2015-06-30 15:31:45',
        ];
        $response = new GenericPostResponse($this->getMockRequest(), $data, 200);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidget());

        $this->assertSame('90', $response->getCode());
        $this->assertSame("Successful Processing : Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());
        $this->assertSame('eyJhciI6InNvbWVhY2NvdW50cmVnaXN0cmF0aW9uIiwicGMiOiJDQy5EQiJ9', $response->getCardReference());
        $this->assertSame('some identification unique ID', $response->getTransactionReference());
        $this->assertSame('some identification unique ID', $response->getIdentificationUniqueId());
        $this->assertSame('some identification transaction ID', $response->getTransactionId());
        $this->assertSame('some identification transaction ID', $response->getIdentificationTransactionId());
        $this->assertSame('someaccountregistration', $response->getAccountRegistration());
        $this->assertSame('some identification short ID', $response->getIdentificationShortId());
        $this->assertSame('Test shopper', $response->getIdentificationShopperId());
        $this->assertSame('Successful Processing', $response->getProcessingReason());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getProcessingReturn());
        $this->assertSame('ACK', $response->getProcessingResult());
        $this->assertSame('CC.DB.90.00', $response->getProcessingCode());
        $this->assertSame('00', $response->getProcessingReasonCode());
        $this->assertSame('90', $response->getProcessingStatusCode());
        $this->assertSame('90', $response->acquireProcessingStatusCode());
        $this->assertSame('000.100.110', $response->getProcessingReturnCode());
        $this->assertSame('CC.DB', $response->getPaymentCode());
        $this->assertSame('ACK', $response->getPostValidationErrorCode());
    }


    public function testHttpErrorResponse()
    {
        $response = new GenericPostResponse($this->getMockRequest(), $this->dataVoidOk, 400);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidget());
    }


    public function testInvalidRequestResponse()
    {
        $data = [
                    'POST_VALIDATION' => '2020',
                    'P3_VALIDATION' => '2020',
            ];
        $response = new GenericPostResponse($this->getMockRequest(), $data, 200);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidget());

        $this->assertSame('2020', $response->getCode());
        $this->assertSame('', $response->getMessage());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getIdentificationUniqueId());
        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getIdentificationTransactionId());
        $this->assertNull($response->getAccountRegistration());
        $this->assertNull($response->getIdentificationShortId());
        $this->assertNull($response->getIdentificationShopperId());
        $this->assertNull($response->getProcessingReason());
        $this->assertNull($response->getProcessingReturn());
        $this->assertNull($response->getProcessingResult());
        $this->assertNull($response->getProcessingCode());
        $this->assertNull($response->getProcessingReasonCode());
        $this->assertNull($response->getProcessingStatusCode());
        $this->assertNull($response->acquireProcessingStatusCode());
        $this->assertNull($response->getProcessingReturnCode());
        $this->assertNull($response->getPaymentCode());
        $this->assertSame('2020', $response->getPostValidationErrorCode());
    }
}
