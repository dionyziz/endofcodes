<?php
    if ( isset( $_GET[ 'user_used' ] ) ) {
        ?><p>Username already exists</p><?php
    }
    else if ( isset( $_GET[ 'mail_used' ] ) ) {
        ?><p>Mail is already used</p><?php
    }
    else if ( isset( $_GET[ 'empty' ] ) ) {
        ?><p>Please fill all the forms.</p><?php
    }
?>

<form action="index.php?resource=user&method=create" method="post">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" />
    <label for="password">Password</label>
    <input type="password" id="password" name="password" />
    <label for="email">Email</label>
    <input type="text" id="email" name="email" />
</form>
