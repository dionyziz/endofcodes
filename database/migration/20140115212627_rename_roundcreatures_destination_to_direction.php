<?php
    require_once 'migrate.php';

    Migration::addField( 
        'roundcreatures', 
        'destination', 
        'direction', 
        'NUM("NONE","NORTH","EAST","SOUTH","WEST") COLLATE utf8_unicode_ci NOT NULL'
    );
?>
