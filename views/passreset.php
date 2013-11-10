<?php
    include 'views/header.php';
?>

<form action="index.php?resource=user&method=update" method="POST">
    <label for="password">New password</label>
    <?php
        if ( isset( $_GET[ 'small_pass' ] ) ) {
            ?><p>Your password should be at least 7 characters long</p><?php
        }
    ?>
    <input type="password" name="password" id="password" />
    <input type="submit" value="Change password" />
</form>

<?php
    include 'views/footer.php';
?>
