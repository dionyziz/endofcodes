<?php
    include 'migrate.php';

    migrate( 
        array( 
            'ALTER TABLE
                users
            ADD
                dob date NOT NULL'
        ) 
    );
?>
