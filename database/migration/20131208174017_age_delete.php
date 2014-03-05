<?php
    require_once 'migrate.php';

    Migration::dropField( 'users', 'age' );
?>
