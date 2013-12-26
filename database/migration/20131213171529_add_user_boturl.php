<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                users
            ADD COLUMN
                boturl VARCHAR(100) NOT NULL;'
        )
    );
?>
