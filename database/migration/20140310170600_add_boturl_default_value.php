<?php
    require_once 'migrate.php';

    Migration::alterField( 'users', 'boturl', 'boturl', "varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''" );
?>
