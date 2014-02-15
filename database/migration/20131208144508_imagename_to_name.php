<?php
    require 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                images
            DROP COLUMN
                imagename',
            "ALTER TABLE
                images
            ADD COLUMN
                `name` text COLLATE utf8_unicode_ci NOT NULL"
        ]
    );
?>
