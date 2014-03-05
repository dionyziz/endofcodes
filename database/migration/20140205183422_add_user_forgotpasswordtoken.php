<?php
    require_once 'migrate.php';
    
    Migration::addField( 'users', 'forgotpasswordtoken', 'text' );
?>
