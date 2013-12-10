<?php
    class Country extends ActiveRecordBase {
        public $name;
        public $id;
        public $shortname;

        public static function findByName( $name ) {
            try {
                $res = db_select_one( "countries", array( 'id' ), compact( "name" ) );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return new Country( $res[ 'id' ] );
        }

        public static function findAll() {
            return db_select( 'countries' );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                $row = db_select_one( "countries", array( 'name', 'shortname' ), compact( "id" ) );
                $this->name = $row[ 'name' ];
                $this->id = $id;
                $this->shortname = $row[ 'shortname' ];
            }
        }
    }
?>
