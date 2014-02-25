<?php
    require 'migrate.php';

    Migration::dropField( 'users', 'age' );
?>
