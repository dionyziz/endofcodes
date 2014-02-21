<?php
    require 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                users
            DROP COLUMN
                age'
        ]
    );
?>
