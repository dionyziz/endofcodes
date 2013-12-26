<?php 
    include_once 'migrate.php';

    migrate( 
        array(
            "ALTER TABLE 
                images
            CHANGE 
                `imageid` `id` INT( 11 ) NOT NULL AUTO_INCREMENT"
        )
    );
?>
