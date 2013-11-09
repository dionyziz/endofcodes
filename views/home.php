<?php
    if ( isset( $username ) ) {
        ?><p>Hello, 
        <?php 
            echo htmlspecialchars( $username );
        ?>.</p>
        <?php
            include 'logoutform.php';
    }
    else {
        ?><a href="index.php?resource=session&amp;method=create">Login</a> or <a href="index.php?resource=user&amp;method=create">Register</a><?php
    }
?>
