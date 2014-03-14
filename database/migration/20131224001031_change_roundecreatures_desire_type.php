<?php
    Migration::alterField( 'roundcreatures', 'desire', 'desire', 'ENUM("NORTH","WEST","EAST","SOUTH","NONE") COLLATE utf8_unicode_ci NOT NULL' );
?>
