<?php
    require_once 'models/game.php';

    class Error extends ActiveRecordBase {
        protected static $attributes = [ 'gameid', 'userid', 'description', 'actual', 'expected' ];
        protected static $tableName = 'errors';
        protected $gameid;
        protected $userid;
        public $game;
        public $user;
        public $description;
        public $actual;
        public $expected;

        public function __construct( $id = false ) {
            if ( $id !== false ) {
                try {
                    $errorArray = dbSelectOne( 'errors', [ 'gameid', 'description', 'userid', 'actual', 'expected' ], compact( 'id' ) );
                }
                catch ( DBExceptionWrongCount $e ) {
                    throw new ModelNotFoundException();
                }
                $this->id = $id;
                $this->gameid = $errorArray[ 'gameid' ];
                $this->userid = $errorArray[ 'userid' ];
                $this->description = $errorArray[ 'description' ];
                $this->actual = $errorArray[ 'actual' ];
                $this->expected = $errorArray[ 'expected' ];
                if ( $this->gameid != 0 ) {
                    $this->game = new Game( $this->gameid );
                }
                $this->user = new User( $this->userid );
            }
        }

        public function onBeforeCreate() {
            if ( isset( $this->game ) ) {
                assert( $this->game instanceof Game/*, '$error->game must be an instance of Game'*/ );
                $this->gameid = $this->game->id;
            }
            else {
                $this->gameid = 0;
            }
            if ( !isset( $this->actual ) ) {
                $this->actual = '';
            }
            if ( !isset( $this->expected ) ) {
                $this->expected = '';
            }
            assert( $this->user instanceof User/*, '$error->user must be an instance of User'*/ );
            $this->userid = $this->user->id;
        }

        public static function findErrorsByGameAndUser( $gameid, $userid ) {
            assert( $gameid > 0/*, 'gameid must be a positive number'*/ );
            assert( $userid > 0/*, 'userid must be a positive number'*/ );

            $errorsArray = dbSelect(
                'errors',
                [ 'id' ],
                compact( "gameid", "userid" )
            );
            return parent::arrayToCollection( $errorsArray );
        }
    }
?>
