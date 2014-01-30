<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                roundcreatures
            ADD COLUMN
                `action` ENUM("NONE","MOVE","ATACK") COLLATE utf8_unicode_ci NOT NULL'
        )
    );
?>
