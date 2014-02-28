<?php
    require_once 'migrate.php';

    Migration::alterField( 'users', 'cookievalue', 'sessionid', 'TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
?>
