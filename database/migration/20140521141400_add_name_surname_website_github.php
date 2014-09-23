<?php
    Migration::addField( 'users', 'name', 'VARCHAR(100) NOT NULL' );
    Migration::addField( 'users', 'surname', 'VARCHAR(100) NOT NULL' );
    Migration::addField( 'users', 'website', 'VARCHAR(100) NOT NULL' );
    Migration::addField( 'users', 'github', 'VARCHAR(100) NOT NULL' );
?>
