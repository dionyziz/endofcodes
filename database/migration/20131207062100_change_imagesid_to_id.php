<?php
    require_once 'migrate.php';

    migrate(
        [
            "ALTER TABLE
                images
            CHANGE
                `imageid` `id` INT( 11 ) NOT NULL AUTO_INCREMENT"
        ]
    );
?>
