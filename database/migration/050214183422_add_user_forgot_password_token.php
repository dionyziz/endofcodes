<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                users
            ADD COLUMN
                forgotPasswordToken text;'
        )
    );
?>
