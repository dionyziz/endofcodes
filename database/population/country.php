<?php
    include_once '../../config/config-local.php';
    include_once '../../models/database.php';
    include_once '../../models/db.php';
    include_once 'countries_array.php';

    global $config; 

    $config = getConfig()[ getEnv( 'ENVIRONMENT' ) ];
    dbInit();
    
    $countries = getCountries();
    $array = [];
    $count = 0;
  
    foreach ( $countries as $key => $value ) {
        try {
            $res = dbInsert( 'countries', [ 'name' => $value, 'shortname' => $key ] );
        }
        catch ( DBException $e ) {
            die( "sql query died with the following error\n" . mysql_error() );
        }
        ++$count;
    }
    
    echo "You imported $count rows out of the " . count( $countries ) . " countries in the table 'countries'.";
?>

