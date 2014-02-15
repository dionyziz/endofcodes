<?php
    include_once 'models/intent.php';

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

        public function __construct( $creature_info = [] ) {
            if ( !empty( $creature_info ) ) {
                $this->exists = true;
                $this->id = $creature_info[ 'creatureid' ];
                $this->locationx = $creature_info[ 'locationx' ];
                $this->locationy = $creature_info[ 'locationy' ];
                $this->hp = $creature_info[ 'hp' ];
                $this->alive = $this->hp > 0;
                $action = actionStringToConst( $creature_info[ 'action' ] );
                $direction = directionStringToConst( $creature_info[ 'direction' ] );
                $this->intent = new Intent( $action, $direction );
                $this->intent->creature = $this;
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

        public function onBeforeSave() {
            if ( !is_int( $this->id ) ) {
                throw new ModelValidationException( 'id_not_valid' );
            }
            if ( !is_int( $this->user->id ) ) {
                throw new ModelValidationException( 'userid_not_valid' );
            }
            if ( !is_int( $this->game->id ) ) {
                throw new ModelValidationException( 'gameid_not_valid' );
            }
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
