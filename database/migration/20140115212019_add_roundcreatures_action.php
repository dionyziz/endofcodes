<?php
    require_once 'migrate.php';

    Migration::addField( 'roundcreatures', 'action', 'ENUM("NONE","MOVE","ATACK") COLLATE utf8_unicode_ci NOT NULL' );
?>
