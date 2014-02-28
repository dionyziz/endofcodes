<?php
    require_once 'migrate.php';

    Migration::alterField( 'creatures', 'id', 'id', 'int(11) NOT NULL' );
?>
