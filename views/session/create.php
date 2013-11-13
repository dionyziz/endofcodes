<?php
    include 'views/header.php';
    if ( isset( $error ) ) {
        ?><p>Invalid username or password!</p><?php
    }
    else if ( isset( $empty_user ) ) {
        ?><p>Please fill the username form.</p><?php
    }
    else if ( isset( $empty_pass ) ) {
        ?><p>Please fill the password form.</p><?php
    }
?>

<form action="index.php?resource=session&amp;method=create" method="POST">
    <label for="username">Username</label>
    <p><input type="text" name="username" id="username" /></p>
    <label for="password">Password</label>
    <p><input type="password" name="password" id="password" /></p>
    <p><input type="submit" value="Login" /></p>
</form>
<?php
    include 'views/footer.php';
?>
