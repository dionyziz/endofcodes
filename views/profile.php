<?php
    include 'views/header.php';
?>

<h1><?php
        echo htmlspecialchars( $credentials[ 'username' ] );
?></h1>

<p>Contact: <?php
    echo htmlspecialchars( $credentials[ 'email' ] );
?></p>

<?php
    if ( $_SESSION[ 'user' ][ 'userid' ] === $credentials[ 'userid' ] ) {
        ?><p>Want to <a href="index.php?resource=user&method=update">change password</a>?</p>
        <form action="index.php?resource=user&method=delete" method="POST">
            <input type="submit" value="Delete your account" />
        </form><?php
    }
?>

<?php
    include 'views/footer.php';
?>
