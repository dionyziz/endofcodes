<?php
    if ( isset( $username ) ) {
        ?><a href="index.php?resource=session&amp;method=delete">Logout</a><?php
    }
    else {
        ?><a href="index.php?resource=session&amp;method=create">Login</a> or <a href="index.php?resource=user&amp;method=create">Register</a><?php
    }
?>
