<?php
    class Error extends ActiveRecordBase {
        protected static $attributes = [ 'gameid', 'userid', 'error' ];
        protected static $tableName = 'errors';
        protected $gameid;
        protected $userid;
        public $game;
        public $user;
        public $error;

        public function __construct( $id = false ) {
            if ( $id !== false ) {
                $errorArray = dbSelectOne( 'errors', [ 'gameid', 'error', 'userid' ], compact( 'id' ) );
                $this->gameid = $errorArray[ 'gameid' ];
                $this->userid = $errorArray[ 'userid' ];
                $this->error = $errorArray[ 'error' ];
                $this->game = new Game( $this->gameid );
                $this->user = new User( $this->userid );
            }
        }

        public function onBeforeCreate() {
            assert( $this->game instanceof Game, '$error->game must be an instance of Game' );
            assert( $this->user instanceof User, '$error->user must be an instance of User' );
            $this->gameid = $this->game->id;
            $this->userid = $this->user->id;
        }

        public static function findErrorsByGameAndUser( $gameid, $userid ) {
            assert( $gameid > 0, 'gameid must be a positive number' );
            assert( $userid > 0, 'userid must be a positive number' );

            $errorsArray = dbSelect(
                'errors',
                [ 'id' ],
                compact( "gameid", "userid" )
            );
            return parent::arrayToCollection( $errorsArray );
        }
    }
?>
