<?php
    include_once 'migrate.php';

    $sql = 'ALTER TABLE
            users
        ADD COLUMN
            age int(3) unsigned NOT NULL;';
    migrate( array( $sql ) );
?>
