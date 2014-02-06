<?php
    include 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                users
            DROP COLUMN
                age'
        ]
    );
?>
