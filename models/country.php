<?php
    class Country {
        public static function getCountryId( $country ) {
            $res = db_select_one( "countries", array( 'id' ), compact( "country" ) );
            return $res[ 'id' ];
        }

        public static function getCountryName( $id ) {
            $res = db_select_one( "countries", array( 'country' ), compact( "id" ) );
            if ( isset( $res[ 'country' ] ) ) {
                return $res[ 'country' ];
            }
            return '';
        }

        public static function getAll() {
            return db_select( 'countries' );
        }

        public static function onList( $country ) { 
            $countries = Country::getAll();
            foreach ( $countries as $valid ) {
                if ( $valid[ 'country' ] === $country ) {
                    return true;
                }
            }
            return false;
        }
    }
?>
