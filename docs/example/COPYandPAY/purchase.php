<html>
    <head>
        <title>Omnipay PayUnity Driver Purchase Example page</title>
    </head>
    <body>
        <h1>Omnipay PayUnity Driver Purchase Example page</h1>

<?php
/**
 * Widget generation example
 */

$amount = '3.45';

$urlBase = getenv('PAYUNITY_DRIVER_FOR_OMNIPAY_EXAMPLES_URL_BASE') ?: 'https://your.site.example/path/to/examples';

/** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
$gateway = \Omnipay\Omnipay::create('PayUnity\\COPYandPAY');

$gateway->initialize([
    "securitySender" => getenv('PAYUNITY_SECURITY_SENDER') ?: "696a8f0fabffea91517d0eb0a0bf9c33",
    "transactionChannel" => getenv('PAYUNITY_TRANSACTION_CHANNEL') ?: "52275ebaf361f20a76b038ba4c806991",
    "userLogin" => getenv('PAYUNITY_USER_LOGIN') ?: "1143238d620a572a726fe92eede0d1ab",
    "transactionMode" => "INTEGRATOR_TEST",
    "userPwd" => getenv('PAYUNITY_USER_PWD') ?: "demo",
    'identificationShopperId' => 'Optional identification of customer',
    'identificationInvoiceId' => 'Optional identifier which you also might print on invoice',
    'identificationBulkId' => 'Optional identifier to group some transactions together',
    'testMode' => true,
    'registrationMode' => true,
]);

$card = [
    'firstName' => 'Sam',
    'lastName' => 'Tester',
    'title' => 'DR',
    'salutation' => 'MR',
    'gender' => 'M',
    'birthday' => '1975-01-30',
    'company' => 'Very Limited',
    'address1' => 'Main street 1/1',
    'address2' => 'Central District',
    'city' => 'Wien',
    'state' => 'AT13',
    'country' => 'AT',
    'email' => 'email@example.com',
    'phone' => '+44 1632 960 111',
    'mobile' => '+44 7700 900 222',
    'shippingAddress1' => 'Main Square 2', //Note: Other contact parameters are taken from generic fields (address2...)
];


$response = $gateway->purchase([
    'amount' => $amount,
    'currency' => 'EUR',
    'brands' => 'VISA MASTER MAESTRO SOFORTUEBERWEISUNG',
    'returnUrl' => $urlBase.'/COPYandPAY/complete_purchase',
    'transactionId' => 'Optional identification of this transaction',
    'description' => 'Just for testing',
    'paymentMemo' => 'Optional MEMO',
    'card' => $card, //Optional
])->send();

if ( ! $response->haveWidget()) {
    echo '<div>Some error has occurred.</div>';
} else {
    $widget = $response->getWidget();
    echo '<div>You can try to pay us '.$amount.' Euro using the form bellow.</div>'."\n";
    echo $widget;
}
?>

    </body>
</html>
