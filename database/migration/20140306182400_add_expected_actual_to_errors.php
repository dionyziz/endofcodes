<?php
    require_once 'migrate.php';
    
    Migration::addField( 'errors', 'expected', 'text' );
    Migration::addField( 'errors', 'actual', 'text' );
?>
