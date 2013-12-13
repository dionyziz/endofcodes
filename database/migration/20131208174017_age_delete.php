<?php
    include 'migrate.php';

    migrate( 
        array( 
            'ALTER TABLE
                users
            DROP COLUMN
                age'
        )
    );
?>
