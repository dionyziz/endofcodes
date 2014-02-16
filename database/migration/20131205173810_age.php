<?php
    include_once 'migrate.php';

    Migration::addField( 'users', 'age', 'int(3) unsigned NOT NULL' );
?>
