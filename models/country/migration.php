<?php
    
    $sql = array ( 
        "CREATE TABLE 
            'countries' (
                'id' INT PRIMARY KEY,
                'country' TEXT,
                'shortname' TEXT 
            );",
        "ALTER TABLE
            'users'
        ADD
            'countryid' INT";
    );

?>
