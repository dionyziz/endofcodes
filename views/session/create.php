<?php
    include 'views/header.php';
?>

<div id="login"><?php
        if ( isset( $username_empty ) ) {
            ?><p class="error">Please type a username.</p><?php
        }
        else if ( isset( $password_empty ) ) {
            ?><p class="error">Please type a password.</p><?php
        }
    ?>

    <form id="login-form" action="index.php?resource=session&amp;method=create" method="POST">
        <label for="username">Username</label>
        <?php
            if ( isset( $username_wrong ) ) {
                ?><p class="error">Username doesn't exist</p><?php
            }
        ?>
        <p><input type="text" name="username" id="username" /></p>
        <label for="password">Password</label>
        <?php
            if ( isset( $password_wrong ) ) {
                ?><p class="error">Password is incorrect</p><?php
            }
        ?>
        <p><input type="password" name="password" id="password" /></p>
        <input type="hidden" name="token" value="<?php
            echo $token;
        ?>" />
        <p><a href="">Forgot password?</a></p>
        <p><input type="submit" value="Login" /></p>
    </form>
    <p><a href="index.php?resource=user&amp;method=create">Don't have an account?</a></p>
</div>

<?php
    include 'views/footer.php';
?>
