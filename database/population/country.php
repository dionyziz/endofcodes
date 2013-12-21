<?php
    include_once '../../config/config-local.php';
    include_once '../../models/database.php';
    include_once '../../models/db.php';
    include_once 'countries_array.php';
    
    $array = getCountries();
    $counter = 0;
    $countries = count($array); 
    
    foreach ( $array as $key => $value ) {
        $res = dbInsert( 'countries', array( 'name' => $value, 'shortname' => $key ) );
        if ( $res === false ) { 
            die( "sql query died with the following error\n\"" . mysql_error() );
        }
        ++$counter;
    }
    
    echo "You imported $counter rows out of the $countries countries in the table 'countries'.";
?>

