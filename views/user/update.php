<?php
    include 'views/header.php';
?>

<form action="index.php?resource=user&amp;method=update" method="post">
    <label for="password_old">Old password</label>
    <?php
        if ( isset( $old_pass ) ) {
            ?><p>Old password is incorrect</p><?php
        }
    ?>
    <p><input type="password" name="password_old" id="password_old" /></p>
    <?php
        if ( isset( $not_matched ) ) {
            ?><p>Passwords do not match</p><?php
        }
        else if ( isset( $small_pass ) ) {
            ?><p>Your password should be at least 7 characters long</p><?php
        }
    ?>
    <label for="password_new">New password</label>
    <p><input type="password" name="password_new" id="password_new" /></p>
    <label for="password_repeat">Repeat</label>
    <p><input type="password" name="password_repeat" id="password_repeat" /></p>
    <input type="submit" value="Change password" />
</form>

<?php
    include 'views/footer.php';
?>
