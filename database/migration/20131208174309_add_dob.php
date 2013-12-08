<?php
    include 'migrate.php';

    $sql = 'ALTER TABLE
            users
        ADD
            dob date NOT NULL';
    migrate( array( $sql ) );
?>
