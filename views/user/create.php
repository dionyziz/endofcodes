<?php
    include 'views/header.php';
    if ( isset( $empty_user ) ) {
        ?><p>Please fill the username form.</p><?php
    }
    else if ( isset( $empty_pass ) ) {
        ?><p>Please fill the password form.</p><?php
    }
    else if ( isset( $empty_mail ) ) {
        ?><p>Please fill the email form.</p><?php
    }
?>

<form action="index.php?resource=user&amp;method=create" method="post">
    <label for="username">Username</label>
    <?php
        if ( isset( $user_used ) ) {
            ?><p>Username already exists</p><?php
            $val = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'username' ] ) ) {
            $val = $_SESSION[ 'create_post' ][ 'username' ];
            unset( $_SESSION[ 'create_post' ][ 'username' ] );
        }
        else {
            $val = "";
        }
    ?>
    <p><input type="text" id="username" name="username" value="<?php
        echo htmlspecialchars( $val );
    ?>"/></p>
    <label for="password">Password</label>
    <?php
        if ( isset( $small_pass ) ) {
            ?><p>Password should be at least 7 characters long</p><?php
        }
    ?>
    <p><input type="password" id="password" name="password" /></p>
    <label for="email">Email</label>
    <?php
        if ( isset( $mail_used ) ) {
            ?><p>Mail is already used</p><?php
            $val = "";
        }
        else if ( isset( $mail_notvalid ) ) {
            ?><p>This is not a valid email</p><?php
            $val = "";
        }
        else if ( isset( $_SESSION[ 'create_post' ][ 'email' ] ) ) {
            $val = $_SESSION[ 'create_post' ][ 'email' ];
            unset( $_SESSION[ 'create_post' ][ 'email' ] );
        }
        else {
            $val = "";
        }
    ?>
    <p><input type="text" id="email" name="email" value="<?php
        echo htmlspecialchars( $val );
    ?>"/></p>
    <p><input type="submit" value="Register" /></p>
</form>

<?php
    include 'views/footer.php';
?>
