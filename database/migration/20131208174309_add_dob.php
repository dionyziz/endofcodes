<?php
    include 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                users
            ADD
                dob date NOT NULL'
        ]
    );
?>
