<?php
    class Game extends ActiveRecordBase {
        public $id;
        public $created;
        public $width;
        public $height;
        public $rounds;

        public function __construct( $id = false ) {
            if ( $id ) {
                $this->exists = true;
                $game_info = dbSelectOne( 'games', array( 'created', 'width', 'height' ), compact( 'id' ) ); 
                $this->id = $gameid = $id;
                $this->created = $game_info[ 'created' ];
                $this->width = $game_info[ 'width' ];
                $this->height = $game_info[ 'height' ];
                $rounds = dbSelect( 'roundcreatures', array( 'roundid' ), compact( 'gameid' ) );
                for ( $i = 0; $i <= count( $rounds ); ++$i ) {
                    $this->rounds[ $i ] = new Round( $this, $i );
                }
            }
            else {
                $this->rounds = array();
            }
        }

        protected function validate() {
            if ( !is_int( $this->width ) ) {
                throw new ModelValidationException( 'width_not_valid' );
            }
            if ( !is_int( $this->height ) ) {
                throw new ModelValidationException( 'height_not_valid' );
            }
        }

        protected function create() {
            $width = $this->width;
            $height = $this->height;
            $created = $this->created = date('Y-m-d H:i:s');
            $this->id = dbInsert(
                'games',
                compact( 'width', 'height', 'created' )
            );
        }

        public function nextRound() {
            $roundid = count( $this->rounds );
            $this->rounds[ $roundid ] = new Round();
            $this->rounds[ $roundid ]->id = $roundid;
            $this->rounds[ $roundid ]->game = $this;
        }
    }
?>
