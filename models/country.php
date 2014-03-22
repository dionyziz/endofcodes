<?php
    class Country extends ActiveRecordBase {
        protected static $attributes = [ 'shortname', 'name' ];
        public $name;
        public $shortname;
        protected static $tableName = 'countries';

        public function __construct( $id = false ) {
            global $config;

            if ( $id ) {
                try {
                    $row = dbSelectOne( 'countries', [ 'name', 'shortname' ], compact( 'id' ) );
                }
                catch ( DBExceptionWrongCount $e ) {
                    throw new ModelNotFoundException();
                }
                $this->name = $row[ 'name' ];
                $this->id = $id;
                $this->shortname = $row[ 'shortname' ];
                $this->flag = $config[ 'paths' ][ 'flag_path' ] . strtolower( $this->shortname ) . '.' . $config[ 'files' ][ 'flag_extention' ];
            }
        }
    }
?>
