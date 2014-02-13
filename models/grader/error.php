<?php
    class Error extends ActiveRecordBase {
        protected static $attributes = [ 'gameid', 'userid', 'error' ];
        protected static $tableName = 'errors';
        protected $gameid;
        protected $userid;
        public $error;

        public function __construct( $gameid, $userid, $error ) {
            $this->gameid = $gameid;
            $this->userid = $userid;
            $this->error = $error;
        }

        public static function findErrorsByGameAndUser( $gameid, $userid ) {
            $errorsArray = dbSelect(
                'errors',
                [ 'error' ],
                compact( "gameid", "userid" )
            );
            $errorObjects = [];
            foreach ( $errorsArray as $error ) {
                $errorObjects[] = new Error( $gameid, $userid, $error[ 'error' ] );
            }
            return $errorObjects;
        }
    }
?>
