<?php
    include 'header.php';
    if ( isset( $empty ) ) {
        ?><p>Please fill all the forms.</p><?php
    }
?>

<form action="index.php?resource=user&method=create" method="post">
    <label for="username">Username</label>
    <?php
        if ( isset( $user_used ) ) {
            ?><p>Username already exists</p><?php
        }
    ?>
    <p><input type="text" id="username" name="username" /></p>
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
        }
        else if ( isset( $mail_notvalid ) ) {
            ?><p>This is not a valid email</p><?php
        }
    ?>
    <p><input type="text" id="email" name="email" /></p>
    <p><input type="submit" value="Register" /></p>
</form>

<?php
    include 'footer.php';
?>
