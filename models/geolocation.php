<?php
    class Location {
        protected static function info() {
            return unserialize(
                file_get_contents( 
                    'http://www.geoplugin.net/php.gp?ip=' . $_SERVER[ 'REMOTE_ADDR' ] 
                ) 
            ); 
        }

        public static function getCountryCode() {
            $info = self::info();
            return $info[ 'geoplugin_countryCode' ];
        }

        public static function getCountryName() {
            $info = self::info();
            return $info[ 'geoplugin_countryName' ];
        }
    }
?>

