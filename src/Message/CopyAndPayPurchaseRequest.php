<?php

namespace Omnipay\PayUnity\Message;

use Subscribo\Omnipay\Shared\CreditCard;
use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

/**
 * Class CopyAndPayPurchaseRequest
 *
 * @package Omnipay\PayUnity
 *
 * @method \Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse send() send()
 */
class CopyAndPayPurchaseRequest extends AbstractRequest
{
    protected $liveEndpointUrl = 'https://ctpe.net/frontend/GenerateToken';

    protected $testEndpointUrl = 'https://test.ctpe.net/frontend/GenerateToken';

    protected $defaultPaymentType = 'DB';

    /**
     * @return null|string|array
     */
    public function getBrands()
    {
        return $this->getParameter('brands');
    }

    /**
     * @param string|array $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setBrands($value)
    {
        return $this->setParameter('brands', $value);
    }

    /**
     * @return string
     */
    protected function getEndpointUrl()
    {
        return $this->getTestMode() ? $this->testEndpointUrl : $this->liveEndpointUrl;
    }

    /**
     * @param array $data
     * @return CopyAndPayPurchaseResponse
     */
    protected function createResponse($data)
    {
        return new CopyAndPayPurchaseResponse($this, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('securitySender', 'transactionChannel', 'userLogin', 'userPwd', 'amount');
        $transactionMode = $this->getTransactionMode() ?: $this->chooseTransactionMode();
        $paymentType = $this->getPaymentType() ?: $this->choosePaymentType();
        $transactionId = $this->getTransactionId();
        $shopperId = $this->getIdentificationShopperId();
        $invoiceId = $this->getIdentificationInvoiceId();
        $bulkId = $this->getIdentificationBulkId();
        $referenceId = $this->getIdentificationReferenceId();
        $usage = $this->getPresentationUsage();
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
            'PAYMENT.TYPE' => $paymentType,
            'PRESENTATION.AMOUNT' => $this->getAmount(),
            'PRESENTATION.CURRENCY' => $this->getCurrency(),
            'PRESENTATION.USAGE' => 'Some usage',
        ];
        if ($transactionId) {
            $result['IDENTIFICATION.TRANSACTIONID'] = $transactionId;
        }
        if ($shopperId) {
            $result['IDENTIFICATION.SHOPPERID'] = $shopperId;
        }
        if ($invoiceId) {
            $result['IDENTIFICATION.INVOICEID'] = $invoiceId;
        }
        if ($bulkId) {
            $result['IDENTIFICATION.BULKID'] = $bulkId;
        }
        if ($referenceId) {
            $result['IDENTIFICATION.REFERENCEID'] = $referenceId;
        }
        if ($usage) {
            $result['PRESENTATION.USAGE'] = $usage;
        }
        if ($paymentMemo) {
            $result['PAYMENT.MEMO'] = $paymentMemo;
        }
        if ($clientIp) {
            $result['CONTACT.IP'] = $clientIp;
        }
        if ($card) {
            $result = $this->addDataFromCard($card, $result);
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function chooseTransactionMode()
    {
        return $this->getTestMode() ? 'INTEGRATOR_TEST' : 'LIVE';
    }

    /**
     * @return string
     */
    protected function choosePaymentType()
    {
        $paymentType = $this->defaultPaymentType;
        if ($this->getRegistrationMode()) {
            $paymentType = 'RG.'.$paymentType;
        }
        return $paymentType;
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
        return $data;
    }
}
