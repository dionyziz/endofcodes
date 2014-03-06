<?php
    require_once 'migrate.php';

    Migration::dropPrimaryKey( 'errors' );
    Migration::addField( 'errors', 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST' );
?>
