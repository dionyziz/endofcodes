<?php
    class Country {
        public static function getCountryId( $country ) {
            $res = db_select( "countries", array( 'id' ), compact( "country" ) );
            return $res[ 0 ][ 'id' ];
        }

        public static function getCountryName( $id ) {
            $res = db_select( "countries", array( 'country' ), compact( "id" ) );
            if ( isset( $res[ 0 ][ 'country' ] ) ) {
                return $res[ 0 ][ 'country' ];
            }
            return '';
        }
    }
?>
