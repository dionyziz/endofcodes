<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                roundcreatures
            CHANGE
                `destination` `direction` ENUM("NONE","NORTH","EAST","SOUTH","WEST") COLLATE utf8_unicode_ci NOT NULL'
        )
    );
?>
