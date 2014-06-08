<?php
    Migration::dropField( 'users', 'avatarid' );
    Migration::addField( 'users', 'imageid', 'int(11) NOT NULL' );
?>
