<!DOCTYPE html>
<html>
    <head>
        <title>Omnipay PayUnity driver POST recurring authorize example page</title>
    </head>
    <body>
        <h1>Omnipay PayUnity driver POST recurring authorize example page</h1>
<?php

$urlBase = getenv('PAYUNITY_DRIVER_FOR_OMNIPAY_EXAMPLES_URL_BASE') ?: 'https://your.site.example/path/to/examples';

try {
    $amount = '2.15';
    echo '<div>Authorization for amount: '.$amount.'</div>'."\n";

    $cardReference = isset($_POST['reference']) ? $_POST['reference'] : '';

    /** @var \Omnipay\PayUnity\PostGateway $gateway */
    $gateway = \Omnipay\Omnipay::create('PayUnity\\Post');
    $gateway->setTestMode(true);
    $gateway->setSecuritySender(getenv('PAYUNITY_SECURITY_SENDER') ?: '696a8f0fabffea91517d0eb0a0bf9c33');
    $gateway->setTransactionChannel(getenv('PAYUNITY_TRANSACTION_CHANNEL') ?: '52275ebaf361f20a76b038ba4c806991');
    $gateway->setUserLogin(getenv('PAYUNITY_USER_LOGIN') ?: '1143238d620a572a726fe92eede0d1ab');
    $gateway->setUserPwd(getenv('PAYUNITY_USER_PWD') ?: 'demo');


    $request = $gateway->authorize();
    $request->setCardReference($cardReference);
    $request->setAmount($amount);
    $request->setCurrency('EUR');
    $request->setDescription('Test authorization');

    $response = $request->send();
    $transactionReference = $response->getTransactionReference();

    echo '<div>'.($response->isSuccessful() ? 'Success' : 'Failure').'</div>';
    echo '<div>Message: '.$response->getMessage().'</div>';
    echo '<div>Code: '.$response->getCode().'</div>';
    echo '<div>Transaction reference:'.$transactionReference.'</div>';
    echo '<h4>Data:</h4>';
    echo '<code>';
    var_dump($response->getData());
    echo '</code>';
?>
    <form action="<?php echo $urlBase; ?>/Post/purchase" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <button type="submit">Make recurring payment with amount 1.05 Euro</button>
    </form>
    <br>
    <form action="<?php echo $urlBase; ?>/Post/authorize" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <button type="submit">Make recurring authorization with amount 2.15 Euro</button>
    </form>
    <br>
    <form action="<?php echo $urlBase; ?>/Post/capture" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <label for="reference">Transaction reference:</label>
        <input type="text" name="transaction" style="width:40em" value="<?php echo $transactionReference ?>">
        <button type="submit">Partial capture 1.00 Euro</button>
    </form>
    <br>
    <form action="<?php echo $urlBase; ?>/Post/void" method="post" target="_blank">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
        <label for="reference">Transaction reference:</label>
        <input type="text" name="transaction" style="width:40em" value="<?php echo $transactionReference ?>">
        <button type="submit">Void authorization</button>
    </form>
    <br>
<?php

} catch (Exception $e) {
    echo '<div>An error happened. Code: '.$e->getCode().' Message: '.$e->getMessage().'</div>';
}
?>
    </body>
</html>
