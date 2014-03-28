<?php
    Migration::alterField( 'roundcreatures', 'action', 'action', 'ENUM("NONE","MOVE","ATTACK") COLLATE utf8_unicode_ci NOT NULL' );
?>
