<?php
    class Game extends ActiveRecordBase {
        public $id;
        public $created;
        public $width;
        public $height;
        public $rounds;
        public $usersCount;
        public $creaturesPerPlayer;
        public $maxHp;
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
            $multiply = $this->creaturesPerPlayer * $this->usersCount;
            $this->width = rand( 3 * $multiply + 1, 4 * $multiply - 1 );
            $this->height = rand( 3 * $multiply + 1, 4 * $multiply - 1 );
            $this->maxHp = rand( 100, 199 );
            $this->created = date( 'Y-m-d H:i:s' );
        }

        public function nextRound() {
            $roundid = count( $this->rounds );
            $this->rounds[ $roundid ] = new Round();
            $this->rounds[ $roundid ]->id = $roundid;
            $this->rounds[ $roundid ]->game = $this;
        }
    }
?>
