<?php 
    include_once 'migrate.php';

    $sql = "ALTER TABLE
            images
        CHANGE 
            `imageid`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT";
    migrate( array( $sql ) );
?>
