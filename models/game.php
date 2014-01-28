<?php
    class Game extends ActiveRecordBase {
        public $id;
        public $created;
        public $width;
        public $height;
        public $rounds;
        public $users;
        public $creaturesPerPlayer;
        public $maxHp;
        public $grid = array( array() );
        protected $tableName = 'games';
        protected $attributes = array( 'width', 'height', 'created' );

        public function __construct( $id = false ) {
            if ( $id ) {
                $this->exists = true;
                $game_info = dbSelectOne( 'games', array( 'created', 'width', 'height' ), compact( 'id' ) ); 
                $this->id = $gameid = $id;
                $this->created = $game_info[ 'created' ];
                $this->width = $game_info[ 'width' ];
                $this->height = $game_info[ 'height' ];
                $rounds = dbSelect( 'roundcreatures', array( 'roundid' ), compact( 'gameid' ) );
                for ( $i = 0; $i < count( $rounds ); ++$i ) {
                    $this->rounds[ $i ] = new Round( $this, $i );
                }
            }
            else {
                $this->rounds = array();
            }
        }

        protected function onBeforeCreate() {
            $this->creaturesPerPlayer = rand( 100, 199 );
            $multiply = $this->creaturesPerPlayer * count( $this->users );
            $this->width = rand( 3 * $multiply + 1, 4 * $multiply - 1 );
            $this->height = rand( 3 * $multiply + 1, 4 * $multiply - 1 );
            $this->maxHp = rand( 100, 199 );
            $this->created = date( 'Y-m-d H:i:s' );
        }

        public function genesis() {
            $this->rounds[ 0 ] = new Round();
            $id = 0;
            for ( $i = 0; $i < count( $this->users ); ++$i ) {
                for ( $j = 0; $j < $this->creaturesPerPlayer; ++$j, ++$id ) {
                    $creature = new Creature();
                    $creature->id = $id;
                    $creature->user = $this->users[ $i ];
                    $creature->game = $this;
                    $creature->hp = $this->maxHp;
                    $creature->alive = true;
                    $creature->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
                    while ( 1 ) {
                        $x = rand( 0, $this->width - 1 );
                        $y = rand( 0, $this->height - 1 );
                        if ( !isset( $this->grid[ $x ][ $y ] ) ) {
                            $creature->locationx = $x;
                            $creature->locationy = $y;
                            $this->grid[ $x ][ $y ] = $creature;
                            break;
                        }
                    }
                    $this->rounds[ 0 ]->creatures[ $id ] = $creature;
                }
            }
        }

        public function nextRound() {
            $roundid = count( $this->rounds );
            $this->rounds[ $roundid ] = new Round();
            $this->rounds[ $roundid ]->id = $roundid;
            $this->rounds[ $roundid ]->game = $this;
        }
    }
?>
