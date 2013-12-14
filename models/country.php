<?php
    class Country extends ActiveRecordBase {
        public $name;
        public $id;
        public $shortname;
        protected $tableName = 'countries';

        public static function findAll() { 
            return dbSelect( 'countries' );
        }

        public function __construct( $id = false ) {
            if ( $id ) {
                try {
                    $row = dbSelectOne( "countries", array( 'name', 'shortname' ), compact( "id" ) );
                }
                catch ( DBException $e ) {
                    throw new ModelNotFoundException();
                }
                $this->name = $row[ 'name' ];
                $this->id = $id;
                $this->shortname = $row[ 'shortname' ];
                global $config;
                $this->flag = $config[ 'paths' ][ 'flag_path' ] . $this->shortname . '.' . $config[ 'files' ][ 'flag_extention' ];
            }
        }
    }
?>
