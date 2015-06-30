<html>
    <head>
        <title>Omnipay PayUnity Driver Complete Purchase Example page</title>
    </head>
    <body>
        <h1>Omnipay PayUnity Driver Complete Purchase Example page</h1>

<?php
/**
 * Code example for page for return url
 */

$urlBase = getenv('PAYUNITY_DRIVER_FOR_OMNIPAY_EXAMPLES_URL_BASE') ?: 'https://your.site.example/path/to/examples';

/** @var \Omnipay\PayUnity\COPYandPAYGateway $gateway */
$gateway = \Omnipay\Omnipay::create('PayUnity\\COPYandPAY');

$gateway->initialize([
    'testMode' => true,
]);

/** @var \Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse $response */
$response = $gateway->completePurchase()->send();

if ($response->isSuccessful()) {
    echo '<div>Success!</div>';
    echo '<div>Transaction Reference:'.$response->getTransactionReference().'</div>';
    echo '<div>Transaction ID:'.$response->getTransactionId().'</div>';
    echo '<div>Card Reference:'.$response->getCardReference().'</div>';
    echo '<div>Identification Unique ID:'.$response->getIdentificationUniqueId().'</div>';
    echo '<div>Identification Short ID:'.$response->getIdentificationShortId().'</div>';
    echo '<div>Identification Shopper ID:'.$response->getIdentificationShopperId().'</div>';
    echo '<div>Identification Transaction ID:'.$response->getIdentificationTransactionId().'</div>';
?>
    <form action="<?php echo $urlBase; ?>/Post/prepare" method="post" target="_blank">
        <input type="hidden" name="reference" value="<?php echo $response->getCardReference(); ?>">
        <input type="hidden" name="transaction" value="<?php echo $response->getTransactionReference(); ?>">
        <button type="submit">Post operations</button>
    </form>
<?php
} else {
    echo '<div>Something has happened.</div>';
    $message = $response->getMessage();
    if ($message) {
        echo '<div>Message: '.$message.'</div>';
    }
}
?>

    </body>
</html>
