<?php
    class Creature extends ActiveRecordBase {
        public $id;
        public $game;
        public $user;
        public $round;
        public $x; 
        public $y;
        public $intent;

        public function __construct( $id, $userid, $gameid ) {
            $this->userid = $userid;
            $this->gameid = $gameid;
            $this->id = $id;
        }

        public function validate() {
            if ( !is_int( $this->id ) ) {
                throw new ModelValidationException( 'id_not_valid' );
            }
            if ( !is_int( $this->userid ) ) {
                throw new ModelValidationException( 'userid_not_valid' );
            }
            if ( !is_int( $this->gameid ) ) {
                throw new ModelValidationException( 'gameid_not_valid' );
            }
        }

        protected function create() {
            $gameid = $this->gameid;
            $userid = $this->userid;
            $id = $this->id;
            $this->exists = true;
            dbInsert( 
                'creatures',
                compact( 'id', 'gameid', 'userid' )
            );
        }
    }
?>
