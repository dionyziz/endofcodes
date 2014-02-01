<?php
    include_once 'models/country.php';
    
    class CountryTest extends UnitTestWithUser {
        public function testCountryConstruct() {
            $randomId = 233;
            $created = true;
            try {
                $country = new Country( $randomId );
            }
            catch ( ModelNotFoundException $e ) {
                $created = false;
            }
            $id = isset( $country->id );
            $shortname = isset( $country->shortname );
            $name = isset( $country->shortname );
            $this->assertEquals( 
                true, 
                $created, 
                'Problem with the dbSelectOne() function or there is not any import with this id in the db' 
            );
            $this->assertEquals( true, $id, 'country->id of the new country object is empty' );
            $this->assertEquals( true, $shortname, 'country->shortname of the new country object is empty' );
            $this->assertEquals( true, $id, 'country->name of the new country object is empty' );
        }
        public function testFindAll() {
            $randomId = 169;
            $country = new Country( $randomId ); 
            $array = Country::findAll(); 
            $success = isset( $array ); 
            $countryId = "$country->id";
            $this->assertEquals( true, $success, 'dbSelectOne did not work' );
            $this->assertEquals( $array[ $randomId - 1 ][ 'id' ], $countryId, 'Not all countries are imported in the database' );
        }
    }

    return new CountryTest();
?>
