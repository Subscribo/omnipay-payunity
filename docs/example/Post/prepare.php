<!DOCTYPE html>
<html>
    <head>
        <title>Omnipay PayUnity driver prepare POST transaction example page</title>
    </head>
    <body>
        <h1>Omnipay PayUnity driver prepare POST transaction example page</h1>
<?php
    $urlBase = getenv('PAYUNITY_DRIVER_FOR_OMNIPAY_EXAMPLES_URL_BASE') ?: 'https://your.site.example/path/to/examples';
    $cardReference = isset($_POST['reference']) ? $_POST['reference'] : '';
    $transactionReference = isset($_POST['transaction']) ? $_POST['transaction'] : '';
?>
        <form action="<?php echo $urlBase; ?>/Post/purchase" method="post" target="_blank">
            <label for="reference">Card reference:</label>
            <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
            <button type="submit">Make recurring payment with amount 1.05 Euro</button>
        </form>
        <br>
        <form action="<?php echo $urlBase; ?>/Post/void" method="post" target="_blank">
            <label for="reference">Card reference:</label>
            <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
            <label for="reference">Transaction reference:</label>
            <input type="text" name="transaction" style="width:40em" value="<?php echo $transactionReference ?>">
            <button type="submit">Void transaction</button>
        </form>
        <br>
        <form action="<?php echo $urlBase; ?>/Post/refund" method="post" target="_blank">
            <label for="reference">Card reference:</label>
            <input type="text" name="reference" style="width:40em" value="<?php echo $cardReference ?>">
            <label for="reference">Transaction reference:</label>
            <input type="text" name="transaction" style="width:40em" value="<?php echo $transactionReference ?>">
            <button type="submit">Partial refund 0.50 Euro</button>
        </form>
    </body>
</html>
