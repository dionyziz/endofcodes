<?php
    require_once 'models/geolocation.php';

    class GeolocationTest extends UnitTestWithFixtures {
        public function testGetCountryCode() {
            $ip = '83.212.120.21'; // Gunther's IP. Located in Greece.
            $this->assertEquals( 
                'GR', 
                Location::getCountryCode( $ip ), 
                'getCountryCode() must return the true country shortcode, where IP is located' 
            );
            $this->assertThrows( 
                function() {
                    Location::getCountryCode( '192.0.0.1' ); 
                }, 
                'ModelNotFoundException', 
                'GetCountryCode() must return Exception when $_SERVER[ "REMOTE_ADDR" ] does not hold a valid public ip address' 
            );
        }
        public function testGetCountryName() {
            $ip = '83.212.120.21'; // Gunther's IP. Located in Greece.
            $this->assertEquals( 
                'Greece', 
                Location::getCountryName( $ip ), 
                'getCountryName() must return the true country name, where IP is located' 
            );
            $this->assertThrows( 
                function() {
                    Location::getCountryName( '192.0.0.1' ); 
                }, 
                'ModelNotFoundException', 
                'GetCountryName() must return Exception when $_SERVER[ "REMOTE_ADDR" ] does not hold a valid public ip address' 
            );
        }
    }
    
    return new GeolocationTest();
?>
