<?php
    include_once 'migrate.php';

    Migration::addField( 'users', 'cookievalue', 'TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
?>
