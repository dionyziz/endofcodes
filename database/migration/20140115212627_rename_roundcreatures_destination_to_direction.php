<?php
    require_once 'migrate.php';

    Migration::alterField( 
        'roundcreatures', 
        'destination', 
        'direction', 
        'ENUM("NONE","NORTH","EAST","SOUTH","WEST") COLLATE utf8_unicode_ci NOT NULL'
    );
?>
