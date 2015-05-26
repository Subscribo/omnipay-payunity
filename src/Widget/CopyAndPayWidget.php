<?php

namespace Omnipay\PayUnity\Widget;

use Subscribo\Omnipay\Shared\Widget\AbstractWidget;
use Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException;

/**
 * Class CopyAndPayWidget
 *
 * @package Omnipay\PayUnity
 */
class CopyAndPayWidget extends AbstractWidget
{
    /**
     * @param string $token
     * @param bool $testMode
     * @param bool $compressed
     * @param string|null $language For example: en
     * @param string|null $style Possibilities: card plain none
     * @return string
     */
    public static function assembleJavascriptUrl(
        $token,
        $testMode = true,
        $language = null,
        $style = null,
        $compressed = true
    ) {
        $urlBase = $testMode
            ? 'https://test.ctpe.net/frontend/widget/v4/widget.js;jsessionid='
            : 'https://ctpe.net/frontend/widget/v4/widget.js;jsessionid=';
        $url = $urlBase.$token;
        $queryParameters = [];
        if ($testMode and ! $compressed) {
            $queryParameters['compressed'] = 'false';
        }
        if ($language) {
            $queryParameters['language'] = $language;
        }
        if ($style) {
            $queryParameters['style'] = $style;
        }
        if ($queryParameters) {
            $url .= '?'.http_build_query($queryParameters);
        }
        return $url;
    }

    /**
     * @param string $token
     * @param bool $testMode
     * @param string|null $language For example: en
     * @param string|null $style Possibilities: card plain none
     * @param bool $compressed
     * @param bool $asynchronous
     * @return string
     */
    public static function assembleJavascript(
        $token,
        $testMode = true,
        $language = null,
        $style = null,
        $compressed = true,
        $asynchronous = true
    ) {
        $async = $asynchronous ? 'async ' : '';
        $url = static::assembleJavascriptUrl($token, $testMode, $language, $style, $compressed);
        $result = '<script '.$async.'src="'.$url.'" ></script>';
        return $result;
    }

    /**
     * @param string $token
     * @param string $returnUrl absolute url for processing the result
     * @param array|string $brands
     * @return string
     */
    public static function assembleHtmlForm($token, $returnUrl, $brands)
    {
        if (is_array($brands)) {
            $brands = implode(' ', $brands);
        }
        $result = '<form action="'.$returnUrl.'" id="'.$token.'">'.$brands.'</form>';
        return $result;
    }

    /**
     * @param array $parameters
     * @return string
     * @throws WidgetInvalidRenderingParametersException
     */
    public function render($parameters = [])
    {
        $result = $this->renderJavascript($parameters)
                    ."\n"
                    .$this->renderHtmlForm($parameters);
        return $result;
    }
    
    public function renderHtmlForm($parameters = [])
    {
        $data = $this->checkParameters($parameters);
        $result = static::assembleHtmlForm($data['transactionToken'], $data['returnUrl'], $data['brands']);
        return $result;
    }
    
    public function renderJavascript($parameters = [])
    {
        $data = $this->checkParameters($parameters);
        $result = static::assembleJavascript(
            $data['transactionToken'],
            $data['testMode'],
            $data['language'],
            $data['style'],
            $data['loadCompressedJavascript'],
            $data['loadJavascriptAsynchronously']
        );
        return $result;
    }


    public function getDefaultParameters()
    {
        return [
            'transactionToken' => null,
            'returnUrl' => null,
            'testMode' => [false, true],
            'brands' => null,
            'language' => '',
            'style' => [null, 'card', 'plain', 'none'],
            'loadCompressedJavascript' => [true, false],
            'loadJavascriptAsynchronously' => [true, false],
        ];
    }


    public function getRequiredParameters()
    {
        return ['transactionToken', 'returnUrl', 'brands'];
    }

    /**
     * @return string|null
     */
    public function getTransactionToken()
    {
        return $this->getParameter('transactionToken');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionToken($value)
    {
        return $this->setParameter('transactionToken', $value);
    }

    /**
     * @return string|null
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * @return bool
     */
    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }

    /**
     * @return string|array|null
     */
    public function getBrands()
    {
        return $this->getParameter('brands');
    }

    /**
     * @param string|array $value
     * @return $this
     */
    public function setBrands($value)
    {
        return $this->setParameter('brands', $value);
    }

    /**
     * @return string|null
     */
    public function getStyle()
    {
        return $this->getParameter('style');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setStyle($value)
    {
        return $this->setParameter('style', $value);
    }

    /**
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * @return bool
     */
    public function getLoadCompressedJavascript()
    {
        return $this->getParameter('loadCompressedJavascript');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setLoadCompressedJavascript($value)
    {
        return $this->setParameter('loadCompressedJavascript', $value);
    }

    /**
     * @return bool
     */
    public function getLoadJavascriptAsynchronously()
    {
        return $this->getParameter('loadJavascriptAsynchronously');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setLoadJavascriptAsynchronously($value)
    {
        return $this->setParameter('loadJavascriptAsynchronously', $value);
    }
}
