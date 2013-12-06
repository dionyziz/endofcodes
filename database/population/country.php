<?php
    include '../../config/config-local.php';
    include '../../models/database.php';
    include 'countries_array.php';
    
    $array = array();
    $count = 0;
    $keys = array_keys( $countries );
  
    foreach ( $keys as $key ) {
        $value = $countries[$key];
        $array[] = "INSERT INTO 
                        `countries` (`id`, `country`, `shortname`) 
                    VALUES 
                        (NULL, '$value', '$key');";
    }
    
    foreach( $array as $sql ) {
        $res = mysql_query( $sql );
        if ( $res === false ) {
            die( "sql query died with the following error\n\"" . mysql_error() );
        }
        ++$count; 
    }
    
    echo "You imported $count raws out of the 239 countries in the table 'countries'.";

?>

