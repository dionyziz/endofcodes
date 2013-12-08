<?php
    include 'migrate.php';

    $sql = 'ALTER TABLE
            users
        DROP COLUMN
            age';
    migrate( array( $sql ) );
?>
