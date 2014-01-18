<?php
    class Creature extends ActiveRecordBase {
        public $id;
        public $game;
        public $user;
        public $round;
        public $locationx; 
        public $locationy;
        public $intent;
        public $alive;
        public $hp;

        public function __construct( $creature_info = array() ) {
            if ( !empty( $creature_info ) ) {
                $this->exists = true;
                $this->id = $creature_info[ 'creatureid' ];
                $this->locationx = $creature_info[ 'locationx' ];
                $this->locationy = $creature_info[ 'locationy' ];
                $this->hp = $creature_info[ 'hp' ];
                $action = convertAction( $creature_info[ 'action' ] );
                $direction = convertDirection( $creature_info[ 'direction' ] );
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

        protected function create() {
            $gameid = $this->game->id;
            $userid = $this->user->id;
            $id = $this->id;
            $this->exists = true;
            dbInsert( 
                'creatures',
                compact( 'id', 'gameid', 'userid' )
            );
        }
    }
?>
