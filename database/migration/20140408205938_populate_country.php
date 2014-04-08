<?php
    require_once 'models/country.php';
    require_once 'population/countries_array.php';

    $countries = getCountries();

    foreach ( $countries as $countryShortname => $countryName ) {
        $country = new Country( '', $countryName, $countryShortname );
        $country->save();
    }
?>
