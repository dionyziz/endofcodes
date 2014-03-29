<?php
    require_once 'models/intent.php';

    class Creature extends ActiveRecordBase {
        public $game;
        public $user;
        public $round;
        public $locationx;
        public $locationy;
        public $intent;
        public $alive;
        public $hp;
        protected $gameid;
        protected $userid;
        protected static $attributes = [ 'id', 'gameid', 'userid' ];
        protected static $tableName = 'creatures';

        public static function saveMulti( $creatures ) {
            $rows = [];
            foreach ( $creatures as $creature ) {
                $id = $creature->id;
                $gameid = $creature->game->id;
                $userid = $creature->user->id;
                $rows[] = compact( 'id', 'gameid', 'userid' );
            }
            dbInsertMulti( 'creatures', $rows );
        }

        public static function selectUseridMulti( $creatures ) {
            $wheres = [];
            foreach ( $creatures as $creature ) {
                $id = $creature->id;
                $gameid = $creature->game->id;
                $wheres[] = compact( 'id', 'gameid' );
            }
            try {
                $rows = dbSelectMulti( 'creatures', [ 'userid' ], $wheres );
            }
            catch ( DBException $e ) {
                throw new ModelNotFoundException();
            }
            return $rows;
        }

        public function __construct( $a = false, $b = false, $c = false ) {
            if ( is_array( $a ) ) {
                $creatureInfo = $a;
                $this->exists = true;
                $this->id = $creatureInfo[ 'creatureid' ];
                $this->locationx = $creatureInfo[ 'locationx' ];
                $this->locationy = $creatureInfo[ 'locationy' ];
                $this->hp = $creatureInfo[ 'hp' ];
                $this->alive = $this->hp > 0;
                $action = actionStringToConst( $creatureInfo[ 'action' ] );
                $direction = directionStringToConst( $creatureInfo[ 'direction' ] );
                $this->intent = new Intent( $action, $direction );
                $this->intent->creature = $this;
            }
            else if ( $a !== false && $b !== false && $c !== false ) {
                $id = $a;
                $userid = $b;
                $gameid = $c;
                try {
                    $creatureInfo = dbSelectOne( 'creatures', [ 'id' ], compact( 'id', 'userid', 'gameid' ) );
                    $this->id = $a;
                    $this->user = new User( $userid );
                    $this->game = new Game( $gameid );
                }
                catch ( DBExceptionWrongCount $e ) {
                    throw new ModelNotFoundException();
                }
            }
            else {
                $this->intent = new Intent();
                $this->alive = true;
            }
        }

        public function toJson() {
            return json_encode( $this->jsonSerialize() );
        }

        public function jsonSerialize() {
            $hp = $this->hp;
            $x = $this->locationx;
            $y = $this->locationy;
            $userid = $this->user->id;
            $creatureid = $this->id;

            return compact( 'creatureid', 'userid', 'x', 'y', 'hp' );
        }

        public function kill() {
            $this->alive = false;
            $this->intent = new Intent();
            $this->hp = 0;
        }

        protected function onBeforeCreate() {
            $this->gameid = $this->game->id;
            $this->userid = $this->user->id;
        }
    }

    class CreatureOutOfBoundsException extends GameException {
        public $creature;

        public function __construct( $creature ) {
            $this->creature = $creature;
            parent::__construct( "Creature $creature->id in location ($creature->locationx, $creature->locationy) "
                               . "of user " . $creature->user->id . " tried to go out of bounds" );
        }
    }
?>
