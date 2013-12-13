<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                countries
            CHANGE
                `country` `name` text COLLATE utf8mb4_unicode_ci NOT NULL'
        )
    );
?>
