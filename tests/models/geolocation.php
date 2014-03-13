<?php
    require_once 'models/geolocation.php';

    class GeolocationTest extends UnitTestWithFixtures {
        public function testGetCountryCode() {
            $_SERVER[ 'REMOTE_ADDR' ] = '83.212.120.21'; //Gunther's IP. Located in Greece.
            $this->assertEquals( 
                'GR', 
                Location::getCountryCode(), 
                'getCountryCode() must return the true country shortcode, where IP is located' 
            );
            $_SERVER[ 'REMOTE_ADDR' ] = '192.0.0.1';
            $this->assertThrows( 
                function() {
                    Location::getCountryCode(); 
                }, 
                'ModelNotFoundException', 
                'GetCountryCode() must return Exception when $_SERVER[ "REMOTE_ADDR" ] does not hold a valid public ip address' 
            );
        }
        public function testGetCountryName() {
            $_SERVER[ 'REMOTE_ADDR' ] = '83.212.120.21'; //Gunther's IP. Located in Greece.
            $this->assertEquals( 
                'Greece', 
                Location::getCountryName(), 
                'getCountryName() must return the true country name, where IP is located' 
            );
            $_SERVER[ 'REMOTE_ADDR' ] = '192.0.0.1';
            $this->assertThrows( 
                function() {
                    Location::getCountryName(); 
                }, 
                'ModelNotFoundException', 
                'GetCountryName() must return Exception when $_SERVER[ "REMOTE_ADDR" ] does not hold a valid public ip address' 
            );
        }
    }
    
    return new GeolocationTest();
?>
