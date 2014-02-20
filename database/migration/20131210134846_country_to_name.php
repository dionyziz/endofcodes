<?php
    require_once 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                countries
            CHANGE
                `country` `name` text COLLATE utf8_unicode_ci NOT NULL'
        ]
    );
?>
