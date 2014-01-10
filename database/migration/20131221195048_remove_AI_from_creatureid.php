<?php
    include 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                creatures
            CHANGE
                `id` `id` int(11) NOT NULL'
        )
    );
?>
