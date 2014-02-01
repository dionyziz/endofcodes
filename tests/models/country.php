<?php
    include_once 'models/country.php';
    
    class CountryTest extends UnitTestWithUser {
        public function testFindAll() {
            $randomId = 233;
            $country = new Country( $randomId ); 
            $array = Country::findAll(); 
            $countryId = "$country->id";
            $this->assertEquals( $array[ $randomId - 1 ][ 'id' ], $countryId, 'Not all countries are imported in the database' );
        }
    }

    return new CountryTest();
?>
