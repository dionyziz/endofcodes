<?php
    if ( isset( $username ) ) {
        ?><p>Hello, <?php echo $username; ?>.</p>
        <p><a href="index.php?resource=session&amp;method=delete">Logout</a></p><?php
    }
    else {
        ?><a href="index.php?resource=session&amp;method=create">Login</a> or <a href="index.php?resource=user&amp;method=create">Register</a><?php
    }
?>
