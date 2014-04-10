<?php
    Migration::dropField( 'images', 'imagename' );
    Migration::addField( 'images', 'name', 'text COLLATE utf8_unicode_ci NOT NULL' );
?>
