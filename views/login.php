<?php
    if ( isset( $_GET[ 'error' ] ) ) {
        ?><p>Invalid username or password!</p><?php
    }
    else if ( isset ( $_GET[ 'empty' ] ) ) {
        ?><p>Some fields are empty</p><?php
    }
?>

<form action="../index.php?resource=session&method=create" method="post">
    <label for="username">Username</label>
    <p><input type="text" name="username" id="username" /></p>
    <label for="password">Password</label>
    <p><input type="password" name="password" id="password" /></p>
    <p><input type="submit" value="Login" /></p>
</form>
