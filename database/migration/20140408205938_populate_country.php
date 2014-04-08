<?php
    require_once 'models/country.php';
    require_once 'population/countries_array.php';

    $countries = getCountries();

    foreach ( $countries as $shortname => $name ) {
        $country = new Country( '', $name, $shortname );
        $country->save();
    }
?>
