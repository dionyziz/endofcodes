<?php
    require_once 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                users
            ADD COLUMN
                boturl VARCHAR(100) NOT NULL;'
        ]
    );
?>
