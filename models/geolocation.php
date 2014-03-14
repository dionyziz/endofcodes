<?php
    class Location {
        protected static function info( $ip ) {
            return unserialize(
                file_get_contents(
                    'http://www.geoplugin.net/php.gp?ip=' . $ip 
                )
            );
        }

        public static function getCountryCode( $ip ) {
            $info = self::info( $ip );
            if( !empty( $info[ 'geoplugin_countryCode' ] ) ) {
                return $info[ 'geoplugin_countryCode' ];
            }
            throw new ModelNotFoundException();
        }

        public static function getCountryName( $ip ) {
            $info = self::info( $ip );
            if( !empty( $info[ 'geoplugin_countryName' ] ) ) {
                return $info[ 'geoplugin_countryName' ];
            }
            throw new ModelNotFoundException();
        }
    }
?>
