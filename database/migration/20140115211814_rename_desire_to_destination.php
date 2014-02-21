<?php
    require_once 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                roundcreatures
            CHANGE
                `desire` `destination` ENUM("NONE","NORTH","EAST","SOUTH","WEST") COLLATE utf8_unicode_ci NOT NULL'
        ]
    );
?>
