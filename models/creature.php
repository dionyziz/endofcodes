<?php
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
        protected $attributes = array( 'id', 'gameid', 'userid' );
        protected $tableName = 'creatures';

        public function __construct( $creature_info = array() ) {
            if ( !empty( $creature_info ) ) {
                $this->exists = true;
                $this->id = $creature_info[ 'creatureid' ];
                $this->locationx = $creature_info[ 'locationx' ];
                $this->locationy = $creature_info[ 'locationy' ];
                $this->hp = $creature_info[ 'hp' ];
                $action = actionStringToConst( $creature_info[ 'action' ] );
                $direction = directionStringToConst( $creature_info[ 'direction' ] );
                $this->intent = new Intent( $action, $direction );
                $this->intent->creature = $this;
            }
        }

        public function validate() {
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
?>
