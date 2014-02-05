<?php
    include_once 'migrate.php';

    migrate( 
        [ 
            'ALTER TABLE
                users
            ADD COLUMN
                age int(3) unsigned NOT NULL;'
        ]
    );
?>
