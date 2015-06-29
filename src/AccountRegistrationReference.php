<?php

namespace Omnipay\PayUnity;

class AccountRegistrationReference
{
    /** @var string|null  */
    public $accountRegistration;

    /** @var string|null  */
    public $paymentCode;

    /**
     * @param string|null $accountRegistration
     * @param string|null $paymentCode
     */
    public function __construct($accountRegistration = null, $paymentCode = null)
    {
        $this->accountRegistration = $accountRegistration;
        $this->paymentCode = $paymentCode;
    }

    /**
     * @param string $encodedAccountRegistrationReference
     * @return AccountRegistrationReference|null
     */
    public static function rebuild($encodedAccountRegistrationReference)
    {
        if (empty($encodedAccountRegistrationReference)) {
            return null;
        }
        $instance = new static();
        $instance->import($encodedAccountRegistrationReference);

        return $instance->isLoaded() ? $instance : null;
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->accountRegistration and $this->paymentCode;
    }

    /**
     * @param string $encodedAccountRegistrationReference
     */
    public function import($encodedAccountRegistrationReference)
    {
        if (empty($encodedAccountRegistrationReference)) {
            return;
        }
        $decoded = base64_decode($encodedAccountRegistrationReference, true);
        $parsed = json_decode($decoded, true, 2, JSON_BIGINT_AS_STRING);
        $this->accountRegistration = $parsed['ar'];
        $this->paymentCode = $parsed['pc'];
    }

    /**
     * @return string|null
     */
    public function export()
    {
        if (( ! $this->isLoaded())) {
            return null;
        }
        $data = [
            'ar' => $this->accountRegistration,
            'pc' => $this->paymentCode,
        ];

        return base64_encode(json_encode($data));
    }
}
