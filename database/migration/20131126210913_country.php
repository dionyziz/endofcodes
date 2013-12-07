<?php
    include_once '../../config/config-local.php';
    include_once '../../models/database.php';

    $res1 = mysql_query( "CREATE TABLE IF NOT EXISTS 
        `countries` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `country` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `shortname` text COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`)
        ) 
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1"
    );

    $res2 = mysql_query( "ALTER TABLE
            `users` 
        ADD COLUMN
            `countryid` int(4) unsigned NOT NULL;"
    );
    
    if ( $res1 === false || $res2 === false ) {
        die( "SQL query died with the following error\n\"". mysql_error() );
    }
    echo 'good';

?>

