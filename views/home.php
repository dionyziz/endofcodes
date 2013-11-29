<?php
    if ( isset( $username ) ) {
        ?><p>Hello,<a href="index.php?resource=user&amp;method=view&amp;username=<?php
            echo htmlspecialchars( $username );
        ?>"><?php 
            echo htmlspecialchars( $username );
        ?></a>.</p><?php
        include 'views/session/logoutform.php';
    }
?>
