<!DOCTYPE html>
<html>
    <head>
        <title>POST void example page</title>
    </head>
    <body>
        <h1>POST void purchase example page</h1>
<?php

try {
    $cardReference = isset($_POST['reference']) ? $_POST['reference'] : '';
    $transactionReference = isset($_POST['transaction']) ? $_POST['transaction'] : '';

    /** @var \Omnipay\PayUnity\PostGateway $gateway */
    $gateway = \Omnipay\Omnipay::create('PayUnity\\Post');
    $gateway->setTestMode(true);
    $gateway->setSecuritySender('696a8f0fabffea91517d0eb0a0bf9c33');
    $gateway->setTransactionChannel('52275ebaf361f20a76b038ba4c806991');
    $gateway->setUserLogin('1143238d620a572a726fe92eede0d1ab');
    $gateway->setUserPwd('demo');


    $request = $gateway->void();
    $request->setCardReference($cardReference);
    $request->setTransactionReference($transactionReference);

    $response = $request->send();

    echo '<div>'.($response->isSuccessful() ? 'Success' : 'Failure').'</div>';
    echo '<div>Message: '.$response->getMessage().'</div>';
    echo '<div>Code: '.$response->getCode().'</div>';
    echo '<h4>Data:</h4>';
    echo '<code>';
    var_dump($response->getData());
    echo '</code>';

} catch (Exception $e) {
    echo '<div>An error happened. Code: '.$e->getCode().' Message: '.$e->getMessage().'</div>';
}
?>
    </body>
</html>
