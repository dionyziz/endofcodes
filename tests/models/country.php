<?php
    include_once 'models/country.php';
    
    class CountryTest extends UnitTest {
        protected function insertCountry() {
            $countries = array(
                'GR' => 'Greece',
                'UK' => 'England',
                'RU' => 'Russia'
            );
            $shortnames = array_keys( $countries );
            $names = array_values( $countries );
            for ( $i = 0; $i < 2; $i++ ) {
                $country = new Country();
                $country->shortname = $shortnames[ $i ];
                $country->name = $names[ $i ];
                $country->save();
            }
        }
        public function testFindAll() {
            $inserted = true;
            $this->insertCountry();
            $res = Country::findAll(); 
            $arrayExists = is_array( $res );
            $success = count( $res ) > 3;
            $this->assertTrue( $arrayExists, '$Country::findall() did not return an array' );
            $this->assertTrue( $success, '$Country::findall() did not return the as much countries as it should' );
        } 
        public function testCountryConstruct() {
            $created = true;
            $testId = 1;
            $this->insertCountry();
            $country = new Country( $testId );
            $idExists = isset( $country->id );
            $shortnameExists = isset( $country->shortname );
            $nameExists = isset( $country->name );
            $this->assertTrue( $idExists, 'country->id of the new country object is empty' );
            $this->assertEquals ( $testId, $country->id, "'new country()' did not return the appropriate country" );
            $this->assertTrue( $shortnameExists, 'country->shortname of the new country object is empty' );
            $this->assertTrue( $nameExists, 'country->name of the new country object is empty' );
        }
        public function testCountryCreate() {
            $created = true;
            try {
                $this->insertCountry();
            }
            catch ( DBException $e ) {
                $created = false;    
            }
            $this->assertTrue( $created, 'Countries could not be saved' );
        }
    }

    return new CountryTest();
?>
