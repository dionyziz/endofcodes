<?php
    include 'migrate.php';

    Migration::dropField( 'users', 'age' );
?>
