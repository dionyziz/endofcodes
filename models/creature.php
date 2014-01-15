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

        public function __construct( $id ) {
            $this->id = $id;
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
