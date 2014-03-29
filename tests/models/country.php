<?php
    require_once 'models/country.php';

    class CountryTest extends UnitTest {
        protected $countries = [
            'GR' => 'Greece',
            'UK' => 'England',
            'RU' => 'Russia'
        ];

        protected function insertCountries() {
            $countries = $this->countries;
            foreach ( $countries as $shortname => $name ) {
                $country = new Country();
                $country->shortname = $shortname;
                $country->name = $name;
                $country->save();
            }
        }
        public function testFindNonExistentCountry() {
            $caught = false;
            try {
                $country = new Country( 1 );
            }
            catch ( ModelNotFoundException $e ) {
                $caught = true;
            }
            $this->assertTrue( $caught, 'When we try to find a non existent country we must get a ModelNotFoundException' );
        }
        public function testFindAll() {
            $this->insertCountries();
            $countriesArray = Country::findAll();
            $this->assertTrue( is_array( $countriesArray ), 'Country::findAll() must return an array of countries' );
            $this->assertEquals( 3, count( $countriesArray ), 'Country::findAll() did not return as many countries as it should' );
        } 
        public function testCountryConstruct() {
            $countries = $this->countries;
            $testId = 1;
            $shortnames = array_keys( $countries );
            $shortname = $shortnames[ 0 ];
            $this->insertCountries();
            $country = new Country( $testId );
            $this->assertTrue( is_object( $country ), 'new Country() must return an object' );
            $this->assertEquals( $testId, $country->id, 'new Country() must return the appropriate country' );
            $this->assertEquals( $shortname, $country->shortname, ''new Country()' must return the appropriate shortname' );
            $this->assertEquals( $countries[ $shortname ], $country->name, ''new Country()' must return the appropriate name' );
        }
        public function testFindByiShortname() {
            $this->insertCountries();
            $country = Country::findByShortname( 'GR' );
            $this->assertTrue( is_object( $country ), 'findByShortname must return an object' );
            $this->assertSame( 1, $country->id, 'findByShortname must find the correct country' );
            $this->assertEquals( 'GR', $country->shortname, 'findByShortname must return the appropriate shortname' );
            $this->assertEquals( 'Greece', $country->name, 'findByShortname must return the appropriate name' );
        }
    }

    return new CountryTest();
?>
