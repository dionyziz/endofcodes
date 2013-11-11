<?php
    include 'views/header.php';
?>

<form action="index.php?resource=user&amp;method=update" method="POST">
    <label for="password1">Old password</label>
    <?php
        if ( isset( $old_pass ) ) {
            ?><p>Old password is wrong</p><?php
        }
    ?>
    <p><input type="password" name="password1" id="password1" /></p>
    <?php
        if ( isset( $not_matched ) ) {
            ?><p>Passwords do not match</p><?php
        }
        else if ( isset( $small_pass ) ) {
            ?><p>Your password should be at least 7 characters long</p><?php
        }
    ?>
    <label for="password2">New password</label>
    <p><input type="password" name="password2" id="password2" /></p>
    <label for="password3">Repeat</label>
    <p><input type="password" name="password3" id="password3" /></p>
    <input type="submit" value="Change password" />
</form>

<?php
    include 'views/footer.php';
?>
