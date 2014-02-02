<?php
    include_once 'models/country.php';
    
    class CountryTest extends UnitTest {
        protected function insertCountry() {
            $country = new Country();
            $country->shortname = 'GR';
            $country->name = 'Greece';
            $country->save();
            $name = $country->name;
        }
        public function testCountryConstruct() {
            $inserted = $created = true;
            $testId = 1;
            try {
                $this->insertCountry();
            }
            catch ( DBException $e ) {
               $inserted = false; 
            }
            try {
                $country = new Country( $testId );
            }
            catch ( ModelNotFoundException $e ) {
                $created = false;
            }
            $idExists = isset( $country->id );
            $shortnameExists = isset( $country->shortname );
            $nameExists = isset( $country->name );
            $this->assertTrue( $inserted, 'Problem with country save. We cannot insert countries in the db' );
            $this->assertTrue( $created, 'Problem with the dbSelectOne() function or there is not any import with this id in the db' );
            $this->assertTrue( $idExists, 'country->id of the new country object is empty' );
            $this->assertTrue( $shortnameExists, 'country->shortname of the new country object is empty' );
            $this->assertTrue( $nameExists, 'country->name of the new country object is empty' );
        }
        public function testFindAll() {
            $inserted = true;
            $testId = 2;
            try {
                $this->insertCountry();
            }
            catch ( DBException $e ) {
               $inserted = false; 
            }
            $country = new Country( $testId ); 
            $array = Country::findAll(); 
            $success = isset( $array ); 
            $countryId = intval( $array[ $testId - 1 ][ 'id' ] ); 
            $this->assertTrue( $inserted, 'Problem with country save. We cannot insert countries in the db' );
            $this->assertTrue( $success, 'dbSelectOne did not work' );
            $this->assertEquals( $countryId, $country->id, 'Not all countries are imported in the database' );
        }
    }

    return new CountryTest();
?>
