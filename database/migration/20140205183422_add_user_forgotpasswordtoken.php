<?php
    include_once 'migrate.php';

    migrate(
        [ 
            'ALTER TABLE
                users
            ADD COLUMN
                forgotpasswordtoken text;'
        ]
    );
?>
