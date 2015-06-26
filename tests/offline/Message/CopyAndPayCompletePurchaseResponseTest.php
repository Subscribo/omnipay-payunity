<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;

class CopyAndPayCompletePurchaseResponseTest extends TestCase
{

    public function testWaiting()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                'token' => 'D480CB27803A2115D52A03AE9239042C.sbg-vm-fe01',
                'transaction' => [
                    'processing' => [
                        'result' => 'WAITING FOR SHOPPER',
                    ],
                ],
            ],
            200
        );
        $this->assertTrue($response->isWaiting());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertEmpty($response->getCode());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertEmpty($response->getTransactionId());
        $this->assertEmpty($response->getIdentificationTransactionId());
        $this->assertEmpty($response->getIdentificationShopperId());
        $this->assertEmpty($response->getIdentificationUniqueId());
        $this->assertEmpty($response->getIdentificationShortId());
        $this->assertNull($response->getCardReference());
        $this->assertSame($response->getIdentificationTransactionId(), $response->getTransactionId());
    }

    public function testSuccess()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                "transaction" => [
                    "channel" => "c1c021a4bfca258d4da22a655dc42966",
                    "identification" => [
                        "shopperid" => "admin",
                        "shortId" => "7307.0292.8546",
                        "transactionid" => "20130129120736562fb049d9e1aee0686f9005f4515f2e",
                        "uniqueId" => "40288b163c865d30013c86600d6d0002"
                    ],
                    "mode" => "CONNECTOR_TEST",
                    "payment" => [
                        "code" => "CC.DB"
                    ],
                    "processing" => [
                        "code" => "CC.DB.90.00",
                        "reason" => [
                            "code" => "00",
                            "message" => "Successful Processing"
                        ],
                        "result" => "ACK",
                        "return" => [
                            "code" => "000.100.112",
                            "message" => "Request successfully processed in Merchant in Connector Test Mode"
                        ],
                        "timestamp" => "2013-01-29 12 => 55 => 14"
                    ],
                    "response" => "SYNC"
                ]
            ],
            200
        );
        $this->assertFalse($response->isWaiting());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertNotEmpty($response->getCode());
        $this->assertSame('000.100.112', $response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame('Request successfully processed in Merchant in Connector Test Mode', $response->getMessage());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('40288b163c865d30013c86600d6d0002', $response->getTransactionReference());
        $this->assertSame('20130129120736562fb049d9e1aee0686f9005f4515f2e', $response->getIdentificationTransactionId());
        $this->assertSame('admin', $response->getIdentificationShopperId());
        $this->assertSame('40288b163c865d30013c86600d6d0002', $response->getIdentificationUniqueId());
        $this->assertSame('7307.0292.8546', $response->getIdentificationShortId());
        $this->assertSame($response->getIdentificationUniqueId(), $response->getTransactionReference());
        $this->assertSame($response->getIdentificationTransactionId(), $response->getTransactionId());
        $this->assertNull($response->getCardReference());
    }

    public function testRegistrationSuccess()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                "token" => "6D9BADD34D777674B567A93A3C6A1E60.sbg-vm-fe01",
                "transaction" =>  [
                    "account" =>  [
                        "bin" => "401288",
                        "brand" => "VISA",
                        "expiry" =>  [
                            "month" => "07",
                            "year" => "2015"
                        ],
                        "holder" => "John Tester",
                        "last4Digits" => "1881",
                        "registration" => "8a82944a4cfff62d014d012551d30123"
                    ],
                    "channel" => "52275ebaf361f20a76b038ba4c806991",
                    "criterions" => [ ["name" => "mode", "value" => "copyandpay" ] ],
                    "customer" =>  [
                        "address" =>  [
                            "city" => "Wien",
                            "country" => "AT",
                            "state" => "AT13",
                            "street" => "Main street Central District"
                        ] ,
                        "contact" =>  [
                            "email" => "email@example.com",
                            "ip" => "192.0.2.1",
                            "ipCountry" => "at"
                        ]
                    ] ,
                    "identification" =>  [
                        "shopperid" => "Optional identification of customer",
                        "shortId" => "6508.0016.9634",
                        "transactionid" => "Optional identification of this transaction 123",
                        "uniqueId" => "8a82944a4cfff62d014d0125541707c0"
                    ] ,
                    "mode" => "INTEGRATOR_TEST",
                    "payment" =>  [
                        "clearing" =>  [
                            "amount" => "0.45",
                            "currency" => "EUR"
                        ] ,
                        "code" => "CC.DB"
                    ] ,
                    "processing" =>  [
                        "code" => "CC.DB.90.00",
                        "connectorDetails" => [],
                        "reason" =>  [
                            "code" => "00",
                            "message" => "Successful Processing"
                        ] ,
                        "result" => "ACK",
                        "return" =>  [
                            "code" => "000.100.110",
                            "message" => "Request successfully processed in 'Merchant in Integrator Test Mode'"
                        ] ,
                        "timestamp" => "2015-04-28 17:48:53"
                    ] ,
                    "response" => "SYNC"
                ]
            ],
            200
        );
        $this->assertFalse($response->isWaiting());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertNotEmpty($response->getCode());
        $this->assertSame('000.100.110', $response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame("Request successfully processed in 'Merchant in Integrator Test Mode'", $response->getMessage());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('8a82944a4cfff62d014d0125541707c0', $response->getTransactionReference());
        $this->assertSame('Optional identification of this transaction 123', $response->getIdentificationTransactionId());
        $this->assertSame('Optional identification of customer', $response->getIdentificationShopperId());
        $this->assertSame('8a82944a4cfff62d014d0125541707c0', $response->getIdentificationUniqueId());
        $this->assertSame('6508.0016.9634', $response->getIdentificationShortId());
        $this->assertSame($response->getIdentificationUniqueId(), $response->getTransactionReference());
        $this->assertSame($response->getIdentificationTransactionId(), $response->getTransactionId());
        $expectedCardReference = 'eyJyZWdpc3RyYXRpb24iOiI4YTgyOTQ0YTRjZmZmNjJkMDE0ZDAxMjU1MWQzMDEyMyIsImNvZGUiOiJDQy5EQiJ9';
        $this->assertSame($expectedCardReference, $response->getCardReference());
    }

    public function testRejected()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            [
                "transaction" => [
                    "channel" => "c1c021a4bfca258d4da22a655dc42966",
                    "identification" => [
                        "shopperid" => "admin",
                        "shortId" => "0435.0816.1186",
                        "transactionid" => "20130129120736562fb049d9e1aee0686f9005f4515f2e",
                        "uniqueId" => "40288b163c865d30013c866d69a2002a"
                    ],
                    "mode" => "CONNECTOR_TEST",
                    "payment" => [
                        "code" => "CC.DB"
                    ],
                    "processing" => [
                        "code" => "CC.DB.70.40",
                        "reason" => [
                            "code" => "40",
                            "message" => "Account Validation"
                        ],
                        "result" => "NOK",
                        "return" => [
                            "code" => "100.100.700",
                            "message" => "invalid cc number/brand combination"
                        ],
                        "timestamp" => "2013-01-29 13 => 09 => 42"
                    ],
                    "response" => "SYNC"
                ]
            ],
            200
        );
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertNotEmpty($response->getCode());
        $this->assertSame('100.100.700', $response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame('invalid cc number/brand combination', $response->getMessage());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('40288b163c865d30013c866d69a2002a', $response->getTransactionReference());
        $this->assertSame('20130129120736562fb049d9e1aee0686f9005f4515f2e', $response->getIdentificationTransactionId());
        $this->assertSame('admin', $response->getIdentificationShopperId());
        $this->assertSame('40288b163c865d30013c866d69a2002a', $response->getIdentificationUniqueId());
        $this->assertSame('0435.0816.1186', $response->getIdentificationShortId());
        $this->assertSame($response->getIdentificationUniqueId(), $response->getTransactionReference());
        $this->assertSame($response->getIdentificationTransactionId(), $response->getTransactionId());
    }

    public function testInvalidResponse()
    {
        $response = new CopyAndPayCompletePurchaseResponse(
            $this->getMockRequest(),
            ["errorMessage" => "Invalid or expired token"],
            200
        );
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertEmpty($response->getCode());
        $this->assertNotEmpty($response->getMessage());
        $this->assertSame('Invalid or expired token', $response->getMessage());
        $this->assertEmpty($response->getTransactionReference());
        $this->assertEmpty($response->getTransactionId());
        $this->assertEmpty($response->getIdentificationTransactionId());
        $this->assertEmpty($response->getIdentificationShopperId());
        $this->assertEmpty($response->getIdentificationUniqueId());
        $this->assertEmpty($response->getIdentificationShortId());
        $this->assertSame($response->getIdentificationUniqueId(), $response->getTransactionReference());
    }

}
