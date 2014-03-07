<?php
    require_once 'migrate.php';

    Migration::alterField( 'errors', 'error', 'error', 'varchar(200) COLLATE utf8_unicode_ci NOT NULL' );
?>
