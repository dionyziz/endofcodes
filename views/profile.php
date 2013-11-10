<?php
    include 'views/header.php';
?>

<h1><?php
        echo $credentials[ 'username' ];
?></h1>

<p>Contact: <?php
    echo $credentials[ 'email' ];
?></p>

<?php
    if ( $_SESSION[ 'user' ][ 'userid' ] === $credentials[ 'userid' ] ) {
        ?><p>Want to <a href="index.php?resource=user&method=update">change password</a>?</p><?php
    }
?>

<?php
    include 'views/footer.php';
?>
