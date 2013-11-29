<?php
    include 'views/header.php';
    if ( isset( $empty_user ) ) {
        ?><p>Please fill the username form.</p><?php
    }
    else if ( isset( $empty_pass ) ) {
        ?><p>Please fill the password form.</p><?php
    }
?>

<form action="index.php?resource=session&amp;method=create" method="POST">
    <label for="username">Username</label>
    <?php
        if ( isset( $wrong_user ) ) {
            ?><p>Username doesn't exist</p><?php
        }
    ?>
    <p><input type="text" name="username" id="username" /></p>
    <label for="password">Password</label>
    <?php
        if ( isset( $wrong_pass ) ) {
            ?><p>Password is incorrect</p><?php
        }
    ?>
    <p><input type="password" name="password" id="password" /></p>
    <p><a href="">Forgot password?</a></p>
    <p><input type="submit" value="Login" /></p>
</form>
<p><a href="index.php?resource=user&method=create">Don't have an account?</a></p>
<?php
    include 'views/footer.php';
?>
