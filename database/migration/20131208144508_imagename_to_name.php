<?php
    include 'migrate.php';

    migrate(
        'ALTER TABLE
            images
        DROP COLUMN
            imagename'
    );

    migrate(
        "ALTER TABLE
            images
        ADD COLUMN
            `name` text COLLATE utf8mb4_unicode_ci NOT NULL"
    );
?>
