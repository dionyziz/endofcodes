<?php
    include 'migrate.php';

    $sql1 = 'ALTER TABLE
            images
        DROP COLUMN
            imagename';

    $sql2 = "ALTER TABLE
            images
        ADD COLUMN
            `name` text COLLATE utf8mb4_unicode_ci NOT NULL";
    migrate( array( $sql1, $sql2 ) );
?>
