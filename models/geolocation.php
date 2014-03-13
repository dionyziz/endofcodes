<?php
    class Location extends ActiveRecordBase {
        protected static function info() {
            return unserialize(
                file_get_contents( 
                    'http://www.geoplugin.net/php.gp?ip=' . $_SERVER[ 'REMOTE_ADDR' ] 
                ) 
            ); 
        }

        public static function getCountryCode() {
            $info = self::info();
            if( !empty( $info[ 'geoplugin_countryCode' ] ) ) {
                return $info[ 'geoplugin_countryCode' ];
            }
            throw new ModelNotFoundException();
        }

        public static function getCountryName() {
            $info = self::info();
            if( !empty( $info[ 'geoplugin_countryName' ] ) ) {
                return $info[ 'geoplugin_countryName' ];
            }
            throw new ModelNotFoundException();
        }
    }
?>

