<?php
    
    include 'models/database.php';

    $res1 = mysql_query( "CREATE TABLE 
        'countries' (
            'id' INT PRIMARY KEY,
            'country' TEXT,
            'shortname' TEXT
        )"
    );

    $res2 = mysql_query( "ALTER TABLE
            'users'
        ADD
            'countryid' INT"
    );

?>
