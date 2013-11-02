<?php
    include 'header.php';
    if ( isset( $username ) ) {
        ?><a href="index.php?resource=session&amp;method=delete">Logout</a><?php
    }
    else {
        ?><a href="views/login.php">Login</a> or <a href="views/register.php">Register</a><?php
    }
    include 'footer.php';
?>
