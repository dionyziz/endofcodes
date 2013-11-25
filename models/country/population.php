<?php

    include 'countries_array.php';
    $array = array(); 
    $keys = array_keys($countries);
    foreach ( $keys as $key ) {
        $value = $countries[$key];
        $array[] = "INSERT INTO 'countries' VALUES ( '', $value, $key )";
    }

?>
