<?php
    include_once '../../config/config-local.php';
    include_once '../../models/database.php';
    include_once '../../models/db.php';
    include_once 'countries_array.php';
    
    $countries = getCountries();
    $array = array();
    $count = 0;
  
    foreach ( $countries as $key => $value ) {
        try {
            $res = dbInsert( 'countries', array( 'name' => $value, 'shortname' => $key ) );
        }
        catch ( DBException $e ) {
            die( "sql query died with the following error\n" . mysql_error() );
        }
        ++$counter;
    }
    
    echo "You imported $counter rows out of the $countries countries in the table 'countries'.";
?>

