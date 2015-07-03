<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\GenericPostResponse;
use Subscribo\Omnipay\Shared\CreditCard;

/**
 * Abstract class AbstractPostRequest
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractPostRequest extends AbstractRequest
{
    protected $endpointUrlBaseTest = 'https://test.payunity.com';

    protected $endpointUrlBaseLive = 'https://payunity.com';

    protected $endpointUrlPath = '/frontend/payment.prc';

    /**
     * @return null|string
     */
    public function getTransactionId()
    {
        return $this->getIdentificationTransactionId();
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTransactionId($value)
    {
        return $this->setIdentificationTransactionId($value);
    }

    /**
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->getIdentificationReferenceId();
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTransactionReference($value)
    {
        return $this->setIdentificationReferenceId($value);
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getPresentationUsage();
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setDescription($value)
    {
        return $this->setPresentationUsage($value);
    }

    /**
     * @return string|null
     */
    public function getIdentificationTransactionId()
    {
        return $this->getParameter('identificationTransactionId');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setIdentificationTransactionId($value)
    {
        return $this->setParameter('identificationTransactionId', $value);
    }

    /**
     * @return string|null
     */
    public function getIdentificationReferenceId()
    {
        return $this->getParameter('identificationReferenceId');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setIdentificationReferenceId($value)
    {
        return $this->setParameter('identificationReferenceId', $value);
    }

    /**
     * @return string|null
     */
    public function getPresentationUsage()
    {
        return $this->getParameter('presentationUsage');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPresentationUsage($value)
    {
        return $this->setParameter('presentationUsage', $value);
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentType($value)
    {
        return $this->setParameter('paymentType', $value);
    }

    /**
     * @return string
     */
    public function getPaymentMemo()
    {
        return $this->getParameter('paymentMemo');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentMemo($value)
    {
        return $this->setParameter('paymentMemo', $value);
    }

    /**
     * @param $data
     * @return string
     */
    protected function getEndpointUrl($data)
    {
        $urlBase = $this->getTestMode() ? $this->endpointUrlBaseTest : $this->endpointUrlBaseLive;
        $urlPath = $this->endpointUrlPath;

        return $urlBase.$urlPath;
    }

    /**
     * @param $data
     * @return array
     */
    protected function getHttpRequestHeaders($data)
    {
        return ['Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'];
    }

    /**
     * @return array
     */
    protected function prepareData()
    {
        $this->validate('securitySender', 'transactionChannel', 'userLogin', 'userPwd');

        $transactionMode = $this->getTransactionMode() ?: $this->chooseTransactionMode();
        $paymentMemo = $this->getPaymentMemo();
        $clientIp = $this->getClientIp();
        $card = $this->getCard();

        $result = [
            'REQUEST.VERSION' => '1.0',
            'SECURITY.SENDER' => $this->getSecuritySender(),
            'TRANSACTION.CHANNEL' => $this->getTransactionChannel(),
            'TRANSACTION.MODE' => $transactionMode,
            'TRANSACTION.RESPONSE' => 'SYNC',
            'USER.LOGIN'  => $this->getUserLogin(),
            'USER.PWD'   => $this->getUserPwd(),
        ];

        if ($paymentMemo) {
            $result['PAYMENT.MEMO'] = $paymentMemo;
        }
        if ($clientIp) {
            $result['CONTACT.IP'] = $clientIp;
        }
        if ($card) {
            $result = $this->addDataFromCard($card, $result);
        }
        $result = $this->addPresentationDetails($result);
        $result = $this->addIdentificationDetails($result);

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addPresentationDetails(array $data)
    {
        $amount = $this->getAmount();
        $currency = $this->getCurrency();
        $usage = $this->getPresentationUsage();

        if (isset($amount)) {
            $data['PRESENTATION.AMOUNT'] = $amount;
        }
        if ($currency) {
            $data['PRESENTATION.CURRENCY'] = $currency;
        }
        if ($usage) {
            $data['PRESENTATION.USAGE'] = $usage;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addIdentificationDetails(array $data)
    {
        $transactionId = $this->getIdentificationTransactionId();
        $shopperId = $this->getIdentificationShopperId();
        $invoiceId = $this->getIdentificationInvoiceId();
        $bulkId = $this->getIdentificationBulkId();
        $referenceId = $this->getIdentificationReferenceId();

        if ($transactionId) {
            $data['IDENTIFICATION.TRANSACTIONID'] = $transactionId;
        }
        if ($shopperId) {
            $data['IDENTIFICATION.SHOPPERID'] = $shopperId;
        }
        if ($invoiceId) {
            $data['IDENTIFICATION.INVOICEID'] = $invoiceId;
        }
        if ($bulkId) {
            $data['IDENTIFICATION.BULKID'] = $bulkId;
        }
        if ($referenceId) {
            $data['IDENTIFICATION.REFERENCEID'] = $referenceId;
        }

        return $data;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $httpResponse
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function processHttpResponse($httpResponse)
    {
        $httpResponse = parent::processHttpResponse($httpResponse);

        $contentType = $httpResponse->getHeaderLine('Content-Type');
        if ('text/plain' === $contentType) {

            return $httpResponse->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        }

        return $httpResponse;
    }

    /**
     * @return string
     */
    protected function chooseTransactionMode()
    {
        return $this->getTestMode() ? 'INTEGRATOR_TEST' : 'LIVE';
    }

    /**
     * @param CreditCard $card
     * @param array $data
     * @return array
     */
    protected function addDataFromCard(CreditCard $card, array $data)
    {
        if ($card->getFirstName()) {
            $data['NAME.GIVEN'] = $card->getFirstName();
        }
        if ($card->getLastName()) {
            $data['NAME.FAMILY'] = $card->getLastName();
        }
        if ($card->getSalutation()) {
            $data['NAME.SALUTATION'] = $card->getSalutation();
        }
        if ($card->getTitle()) {
            $data['NAME.TITLE'] = $card->getTitle();
        }
        if ($card->getGender()) {
            $data['NAME.SEX'] = $card->getGender();
        }
        if ($card->getBirthday()) {
            $data['NAME.BIRTHDATE'] = $card->getBirthday();
        }
        if ($card->getCompany()) {
            $data['NAME.COMPANY'] = $card->getCompany();
        }
        if ($card->getCountry()) {
            $data['ADDRESS.COUNTRY'] = $card->getCountry();
        }
        if ($card->getState()) {
            $data['ADDRESS.STATE'] = $card->getState();
        }
        if ($card->getCity()) {
            $data['ADDRESS.CITY'] = $card->getCity();
        }
        if ($card->getPostcode()) {
            $data['ADDRESS.ZIP'] = $card->getPostcode();
        }
        $street = '';
        if ($card->getAddress1()) {
            $street = $card->getAddress1();
        }
        if ($card->getAddress2()) {
            $street .= "\n".$card->getAddress2();
        }
        $street = trim($street);
        if ($street) {
            $data['ADDRESS.STREET'] = $street;
        }
        if ($card->getEmail()) {
            $data['CONTACT.EMAIL'] = $card->getEmail();
        }
        if ($card->getPhone()) {
            $data['CONTACT.PHONE'] = $card->getPhone();
        }
        if ($card->getMobile()) {
            $data['CONTACT.MOBILE'] = $card->getMobile();
        }
        if ($card->getIdentificationDocumentType() and $card->getIdentificationDocumentNumber()) {
            $data['CUSTOMER.IDENTIFICATION.PAPER'] = $card->getIdentificationDocumentType();
            $data['CUSTOMER.IDENTIFICATION.VALUE'] = $card->getIdentificationDocumentNumber();
        }
        if ($card->getShippingContactDifferences()) {
            $data = $this->addShippingDataFromCard($card, $data);
        }

        return $data;
    }

    protected function addShippingDataFromCard(CreditCard $card, array $data)
    {
        if ($card->getShippingFirstName()) {
            $data['CUSTOMER.SHIPPING.NAME.GIVEN'] = $card->getShippingFirstName();
        }
        if ($card->getShippingLastName()) {
            $data['CUSTOMER.SHIPPING.NAME.FAMILY'] = $card->getShippingLastName();
        }
        $street = '';
        if ($card->getShippingAddress1()) {
            $street = $card->getShippingAddress1();
        }
        if ($card->getShippingAddress2()) {
            $street .= "\n".$card->getShippingAddress2();
        }
        $street = trim($street);
        if ($street) {
            $data['CUSTOMER.SHIPPING.ADDRESS.STREET'] = $street;
        }
        if ($card->getShippingCity()) {
            $data['CUSTOMER.SHIPPING.ADDRESS.CITY'] = $card->getShippingCity();
        }
        if ($card->getShippingPostcode()) {
            $data['CUSTOMER.SHIPPING.ADDRESS.ZIP'] = $card->getShippingPostcode();
        }
        if ($card->getShippingState()) {
            $data['CUSTOMER.SHIPPING.ADDRESS.STATE'] = $card->getShippingState();
        }
        if ($card->getShippingCountry()) {
            $data['CUSTOMER.SHIPPING.ADDRESS.COUNTRY'] = $card->getShippingCountry();
        }
        if ($card->getShippingPhone()) {
            $data['CUSTOMER.SHIPPING.CONTACT.PHONE'] = $card->getShippingPhone();
        }
        if ($card->getShippingMobile()) {
            $data['CUSTOMER.SHIPPING.CONTACT.MOBILE'] = $card->getShippingMobile();
        }

        return $data;
    }
}
