<?php
    include_once 'migrate.php';

    Migration::alterField( 'countries', 'country', 'name', 'text COLLATE utf8_unicode_ci NOT NULL' );
?>
