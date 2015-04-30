<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\AbstractRequest;

class AbstractRequestTest extends TestCase
{
    /**
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::sendData
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::getEndPointUrl
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::createResponse
     */
    public function testSendData()
    {
        $this->setMockHttpResponse('simpleAbstractSuccess.txt');

        $url = 'https://some.api.example/testurl';
        $requestStub = $this->getMockForAbstractClass(
            '\\Omnipay\\PayUnity\\Message\\AbstractRequest',
            [
                $this->getHttpClient(),
                $this->getHttpRequest(),
            ]
        );
        $responseStub = $this->getMockForAbstractClass(
            '\\Omnipay\\PayUnity\\Message\\AbstractResponse',
            [
                $requestStub,
                ['result' => 'test'],
            ]
        );
        $requestStub->expects($this->once())
            ->method('getEndPointUrl')
            ->will($this->returnValue($url));
        $requestStub->expects($this->once())
            ->method('createResponse')
            ->with(['result' => 'test'])
            ->will($this->returnValue($responseStub));
        $this->assertInstanceOf(
            '\\Omnipay\\PayUnity\\Message\\AbstractResponse',
            $requestStub->sendData(['testData' => 'someData'])
        );
    }
}
