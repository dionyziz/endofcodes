<?php
    class Country extends ActiveRecordBase {
        public $name;
        public $id;
        public $shortname;

        public static function getByName( $country ) {
            $res = db_select_one( "countries", array( 'id' ), compact( "country" ) );
            return new Country( $res[ 'id' ] );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                $row = db_select_one( "countries", array( 'country', 'shortname' ), compact( "id" ) );
                $this->name = $row[ 'country' ];
                $this->id = $id;
                $this->shortname = $row[ 'shortname' ];
            }
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
