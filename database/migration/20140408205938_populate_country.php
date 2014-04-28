<?php
    require_once 'models/country.php';
    require_once 'data/countries_array.php';

    $countries = getCountries();

    foreach ( $countries as $countryShortname => $countryName ) {
        $country = new Country();
        $country->name = $countryName;
        $country->shortname = $countryShortname;
        $country->save();
    }
?>
