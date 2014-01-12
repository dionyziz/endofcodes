<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                roundcreatures
            CHANGE
                `desire` `desire` ENUM("NONE","NORTH","EAST","SOUTH","WEST") COLLATE utf8_unicode_ci NOT NULL',
            'ALTER TABLE
                games
            CHANGE
                `created` `created` DATETIME NOT NULL'
        )
    );
?>
