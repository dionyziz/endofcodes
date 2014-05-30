<?php
    class Location {
        public static $URLRetrieverObject = null;

        protected static function info( $ip ) {
            if ( is_null( self::URLRetrieverObject ) ) {
                self::URLRetrieverObject = new URLRetriever();
            }
            $geoInfo = self::URLRetrieverObject->readURL( 'http://www.geoplugin.net/php.gp?ip=' . $ip );
            return unserialize( $geoInfo );
        }

        public static function getCountryCode( $ip ) {
            $info = self::info( $ip );
            if ( !empty( $info[ 'geoplugin_countryCode' ] ) ) {
                return $info[ 'geoplugin_countryCode' ];
            }
            throw new ModelNotFoundException();
        }

        public static function getCountryName( $ip ) {
            $info = self::info( $ip );
            if ( !empty( $info[ 'geoplugin_countryName' ] ) ) {
                return $info[ 'geoplugin_countryName' ];
            }
            throw new ModelNotFoundException();
        }
    }
?>
