<?php
    require_once 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                roundcreatures
            CHANGE
                `desire` `desire` ENUM("NORTH","WEST","EAST","SOUTH","NONE") COLLATE utf8_unicode_ci NOT NULL'
        ]
    );
?>
