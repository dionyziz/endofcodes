<?php
    require_once 'models/country.php';
    $countries = json_decode( file_get_contents( 'database/migration/data/countries.json' ), true );

    foreach ( $countries as $countryShortname => $countryName ) {
        $country = new Country();
        $country->name = $countryName;
        $country->shortname = $countryShortname;
        $country->save();
    }
?>
