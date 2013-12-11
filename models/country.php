<?php
    class Country extends ActiveRecordBase {
        public $name;
        public $id;
        public $shortname;

        public static function findByName( $name ) {
            try {
                $res = dbSelectOne( "countries", array( 'id' ), compact( "name" ) );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return new Country( $res[ 'id' ] );
        }

        public static function findAll() {
            return dbSelect( 'countries' );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                $row = dbSelectOne( "countries", array( 'name', 'shortname' ), compact( "id" ) );
                $this->name = $row[ 'name' ];
                $this->id = $id;
                $this->shortname = $row[ 'shortname' ];
                global $config;
                $this->flag = $config[ 'paths' ][ 'flag_path' ] . $this->shortname . '.' . $config[ 'files' ][ 'flag_extention' ];
            }
        }
    }
?>
