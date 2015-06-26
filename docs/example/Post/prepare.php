<!DOCTYPE html>
<html>
    <head>
        <title>Prepare POST transaction example page</title>
    </head>
    <body>
        <h1>Prepare POST transaction example page</h1>
    <form action="https://your.site.example/example/Post/purchase" method="post">
        <label for="reference">Card reference:</label>
        <input type="text" name="reference" style="width:40em"
               value="<?php echo isset($_POST['reference']) ? $_POST['reference'] : ''; ?>">
        <br>
        <button type="submit" name="action" value="purchase">Make recurring payment with amount 1.05 Euro</button>
    </form>
    </body>
</html>
