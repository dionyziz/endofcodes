<?php
    require 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                creatures
            CHANGE
                `id` `id` int(11) NOT NULL'
        ]
    );
?>
