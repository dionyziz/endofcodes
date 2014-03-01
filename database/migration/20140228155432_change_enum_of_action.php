<?php
    require_once 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                roundcreatures
            CHANGE
                `action` `action` ENUM("NONE","MOVE","ATTACK") COLLATE utf8_unicode_ci NOT NULL'
        ]
    );
?>
