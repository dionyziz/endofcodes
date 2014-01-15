<?php
    class Creature extends ActiveRecordBase {
        public $id;
        public $game;
        public $user;
        public $round;
        public $x; 
        public $y;
        public $intent;
        public $alive;
        public $hp;

        public function __construct( $creature_info = array() ) {
            if ( !empty( $creature_info ) ) {
                $this->exists = true;
                $this->id = $creature_info[ 'creatureid' ];
                $this->x = $creature_info[ 'locationx' ];
                $this->y = $creature_info[ 'locationy' ];
                $this->hp = $creature_info[ 'hp' ];
                $this->intent = new Intent(  
                    'ACTION_' . $creature_info[ 'action' ], 
                    'DIRECTION_' . $creature_info[ 'direction' ] 
                );
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
