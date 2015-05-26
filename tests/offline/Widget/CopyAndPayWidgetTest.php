<?php

namespace Omnipay\PayUnity\Widget;

use PHPUnit_Framework_TestCase;
use Omnipay\PayUnity\Widget\CopyAndPayWidget;

class CopyAndPayWidgetTest extends PHPUnit_Framework_TestCase
{
    public function testSettersAndGetters()
    {
        $widget = new CopyAndPayWidget();
        $defaults = $widget->getDefaultParameters();
        foreach($defaults as $key => $val) {
            $getter = 'get'.ucfirst($key);
            $setter = 'set'.ucfirst($key);
            $this->assertTrue(method_exists($widget, $getter), "Getter '".$getter."' does not exists for default parameter '".$key."'");
            $this->assertTrue(method_exists($widget, $setter), "Setter '".$setter."' does not exists for default parameter '".$key."'");
            $testValue = uniqid();
            $this->assertSame($widget, $widget->$setter($testValue), "Setter '".$setter."' does not have fluent interface'");
            $this->assertSame($testValue, $widget->$getter(), "Getter '".$getter."' does not return the same value as the one provided for setter '".$setter."'");
        }
    }

    public function testEmptyConstruct()
    {
        $widget = new CopyAndPayWidget();
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);

        $this->assertFalse($widget->getTestMode());
        $this->assertSame($widget, $widget->setTestMode(true));
        $this->assertTrue($widget->getTestMode());

        $this->assertNull($widget->getTransactionToken());
        $this->assertNull($widget->getReturnUrl());
        $this->assertNull($widget->getBrands());
        $this->assertSame('', $widget->getLanguage());
        $this->assertTrue($widget->getLoadCompressedJavascript());
        $this->assertTrue($widget->getLoadJavascriptAsynchronously());

        ///No required parameters set
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertFalse($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());

        /// transactionToken set
        $this->assertSame($widget, $widget->setTransactionToken('SomeToken'));
        $this->assertSame('SomeToken', $widget->getTransactionToken());
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertFalse($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());

        /// transactionToken and returnUrl set

        $this->assertSame($widget, $widget->setReturnUrl('https://nonexistent.example/return/url'));
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertFalse($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertTrue($widget->isRenderable(['brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());

        /// returnUrl set

        $this->assertSame($widget, $widget->setTransactionToken(null));
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertFalse($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());

        /// returnUrl and brand set

        $this->assertSame($widget, $widget->setBrands(['MAESTRO', 'VISA']));
        $this->assertSame(['MAESTRO', 'VISA'], $widget->getBrands());
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertFalse($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());

        /// returnUrl, brand and transactionToken set

        $this->assertSame($widget, $widget->setTransactionToken('ab1234'));
        $this->assertNotEmpty((string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertTrue($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertTrue($widget->isRenderable(['brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable());
        $expectedWidget = '<script async src="https://test.ctpe.net/frontend/widget/v4/widget.js;jsessionid=ab1234" ></script>'."\n"
                            .'<form action="https://nonexistent.example/return/url" id="ab1234">MAESTRO VISA</form>';
        $this->assertSame($expectedWidget, (string) $widget);
        $this->assertSame($expectedWidget, $widget->render());


        /// brand and transactionToken set

        $this->assertSame($widget, $widget->setReturnUrl(null));
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertTrue($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertTrue($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());

        /// brand set

        $this->assertSame($widget, $widget->setTransactionToken(null));
        $this->assertNull($widget->getTransactionToken());
        $this->assertSame('', (string) $widget);
        $this->assertFalse($widget->isRenderable(null));
        $this->assertFalse($widget->isRenderable(['returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['brands' => 'VISA', 'returnUrl' => 'https://nonexistent.example/return/url']));
        $this->assertFalse($widget->isRenderable(['transactionToken' => 'abc123', 'brands' => 'VISA']));
        $this->assertTrue($widget->isRenderable(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url', 'brands' => 'VISA']));
        $this->assertFalse($widget->isRenderable());
    }

    public function testConstructWithParameters()
    {
        $widget = new CopyAndPayWidget([
            'returnUrl' => 'https://nonexistent.example/return/url',
            'transactionToken' => '1234567890',
            'brands' => ['VISA', 'MAESTRO'],
            'language' => 'en',
            'style' => 'plain',
            'loadCompressedJavascript' => false,
            'loadJavascriptAsynchronously' => false,
            'testMode' => true,
        ]);
        $this->assertInstanceOf('\\Omnipay\\PayUnity\\Widget\\CopyAndPayWidget', $widget);
        $this->assertTrue($widget->getTestMode());
        $this->assertSame($widget, $widget->setTestMode(false));
        $this->assertFalse($widget->getTestMode());
        $this->assertSame('1234567890', $widget->getTransactionToken());
        $this->assertSame('https://nonexistent.example/return/url', $widget->getReturnUrl());
        $this->assertSame(['VISA', 'MAESTRO'], $widget->getBrands());
        $this->assertSame('en', $widget->getLanguage());
        $this->assertFalse($widget->getLoadCompressedJavascript());
        $this->assertFalse($widget->getLoadJavascriptAsynchronously());
        $expectedWidget = '<script src="https://ctpe.net/frontend/widget/v4/widget.js;jsessionid=1234567890?language=en&style=plain" ></script>'
                            ."\n"
                            .'<form action="https://nonexistent.example/return/url" id="1234567890">VISA MAESTRO</form>';
        $this->assertSame($expectedWidget, (string) $widget);
        $this->assertSame($expectedWidget, $widget->render());
        $widget->setTestMode(true);
        $scriptPart = '<script src="https://test.ctpe.net/frontend/widget/v4/widget.js;jsessionid=1234567890?compressed=false&language=en&style=plain" ></script>';
        $formPart = '<form action="https://nonexistent.example/return/url" id="1234567890">VISA MAESTRO</form>';
        $expectedWidget = $scriptPart."\n".$formPart;
        $this->assertSame($expectedWidget, (string) $widget);
        $this->assertSame($expectedWidget, $widget->render());
        $this->assertSame($scriptPart, $widget->renderJavascript());
        $this->assertSame($formPart, $widget->renderHtmlForm());

        $this->assertSame($widget, $widget->setLanguage(null));
        $this->assertNull($widget->getLanguage());
        $this->assertSame($widget, $widget->setStyle('card'));
        $this->assertSame('card', $widget->getStyle());
        $this->assertSame($widget, $widget->setBrands([]));
        $this->assertSame([], $widget->getBrands());
        $this->assertSame($widget, $widget->setLoadCompressedJavascript(true));
        $this->assertTrue($widget->getLoadCompressedJavascript());
        $this->assertSame($widget, $widget->setLoadJavascriptAsynchronously(true));
        $this->assertTrue($widget->getLoadJavascriptAsynchronously());

    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage Parameters should be an array
     */
    public function testRenderFailureNotArray()
    {
        $widget = new CopyAndPayWidget();
        $widget->render(null);
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage transactionToken
     */
    public function testRenderFailureNoTransactionToken()
    {
        $widget = new CopyAndPayWidget(['returnUrl' => 'https://nonexistent.example/return/url', 'brands' => []]);
        $widget->render();

    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage returnUrl
     */
    public function testRenderFailureReturnUrl()
    {
        $widget = new CopyAndPayWidget(['transactionToken' => 'abc123', 'brands' => 'VISA']);
        $widget->render();

    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage brands
     */
    public function testRenderFailureBrands()
    {
        $widget = new CopyAndPayWidget(['transactionToken' => 'abc123', 'returnUrl' => 'https://nonexistent.example/return/url']);
        $widget->render();

    }

}
