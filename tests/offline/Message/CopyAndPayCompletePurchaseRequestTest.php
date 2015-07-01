<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseRequest;
use Symfony\Component\HttpFoundation\Request;

class CopyAndPayCompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CopyAndPayCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTestMode(true);
    }


    public function testGetData()
    {
        $purchaseResponse = $this->getMockBuilder('Omnipay\\PayUnity\\Message\\CopyAndPayPurchaseResponse')
            ->disableOriginalConstructor()
            ->getMock();
        $purchaseResponse->expects($this->once())
            ->method('getTransactionToken')
            ->will($this->returnValue('33E47BC8E286B472A1299EAC39F4556D.sbg-vm-fe01'));
        $this->request->fill($purchaseResponse);
        $data = $this->request->getData();
        $this->assertSame('33E47BC8E286B472A1299EAC39F4556D.sbg-vm-fe01', $data['transactionToken']);
    }


    public function testGetTokenFromHttpRequest()
    {
        $httpRequest = new Request(['token' => '33E47BC8E286B472A1299EAC39F4556D.sbg-vm-fe01']);
        $request = new CopyAndPayCompletePurchaseRequest($this->getHttpClient(), $httpRequest);
        $data = $request->getData();
        $this->assertSame('33E47BC8E286B472A1299EAC39F4556D.sbg-vm-fe01', $data['transactionToken']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Token
     */
    public function testNoTokenFound()
    {
        $this->request->getData();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSendingNonArrayData()
    {
        $this->request->sendData(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSendingEmptyData()
    {
        $this->request->sendData([]);
    }
}
