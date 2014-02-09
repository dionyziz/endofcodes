<?php
    include_once 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                users
            ADD COLUMN
                forgotpasswordexptime datetime;'
        )
    );
?>
