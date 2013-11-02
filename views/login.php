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
    <input type="text" name="username" id="username" /> 
    <label for="password">Password</label>
    <input type="password" name="password" id="password" />
    <input type="submit" value="Login" />
</form>
