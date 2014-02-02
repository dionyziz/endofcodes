<?php
    include_once 'models/country.php';
    
    class CountryTest extends UnitTest {
        protected function insertCountry() {
            $country = new Country();
            $country->shortname = 'GR';
            $country->name = 'Greece';
            $country->save();
        }
        public function testCountryConstruct() {
            $insert = $created = true;
            $randomId = 1;
            try {
                $this->insertCountry();
            }
            catch ( DBException $e ) {
               $insert = false; 
            }
            try {
                $country = new Country( $randomId );
            }
            catch ( ModelNotFoundException $e ) {
                $created = false;
            }
            $id = isset( $country->id );
            $shortname = isset( $country->shortname );
            $name = isset( $country->shortname );
            $this->assertEquals( true, $insert, 'Problem with dbInsert. We cannot insert countries in the db' );
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
            $insert = true;
            $randomId = 2;
            try {
                $this->insertCountry();
            }
            catch ( DBException $e ) {
               $insert = false; 
            }
            $country = new Country( $randomId ); 
            $array = Country::findAll(); 
            $success = isset( $array ); 
            $countryId = "$country->id";
            $this->assertEquals( true, $insert, 'Problem with dbInsert. We cannot insert countries in the db' );
            $this->assertEquals( true, $success, 'dbSelectOne did not work' );
            $this->assertEquals( $array[ $randomId - 1 ][ 'id' ], $countryId, 'Not all countries are imported in the database' );
        }
    }

    return new CountryTest();
?>
