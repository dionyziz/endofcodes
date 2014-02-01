<?php
    class Round extends ActiveRecordBase {
        public $creatures = array();
        public $id;
        public $game;

        public function __construct( $a = false, $b = false ) {
            if ( $a instanceof Round ) {
                // Clone from existing round: new Round( $oldRound )
                $oldRound = $a;
                $this->game = $oldRound->game;
                $this->id = $oldRound->id + 1;
                foreach ( $oldRound->creatures as $creature ) {
                    $this->creatures[ $creature->id ] = clone $creature;
                    $this->creatures[ $creature->id ]->round = $this;
                }
            }
            else if ( $a !== false && $b !== false ) {
                //find the whole round from database: new Round( $game, $id );
                $game = $a;
                $id = $b;
                $this->exists = true;
                $this->id = $id;
                $this->game = $game;
                $gameid = $game->id;
                $roundid = $id;
                $creatures_info = dbSelect(
                    'roundcreatures',
                    array( 'creatureid', 'action', 'direction', 'hp', 'locationx', 'locationy' ),
                    compact( 'roundid', 'gameid' )
                );
                foreach ( $creatures_info as $i => $creature_info ) {
                    $id = $creature_info[ 'creatureid' ];
                    $user_info = dbSelectOne(
                        'creatures',
                        array( 'userid' ),
                        compact( 'id' )
                    );
                    $user = new User( $user_info[ 'userid' ] );
                    $this->creatures[ $i ] = new Creature( $creature_info );
                    $this->creatures[ $i ]->game = $game;
                    $this->creatures[ $i ]->round = $this;
                    $this->creatures[ $i ]->user = $user;
                }
            }
        }

        protected function create() {
            $gameid = $this->game->id;
            $roundid = $this->id;
            foreach ( $this->creatures as $creature ) {
                $locationx = $creature->locationx;
                $locationy = $creature->locationy;
                $hp = $creature->hp;
                $direction = directionConstToString( $creature->intent->direction );
                $action = actionConstToString( $creature->intent->action );
                $creatureid = $creature->id;
                dbInsert(
                    'roundcreatures',
                    compact( 
                        'gameid', 
                        'roundid', 
                        'locationx', 
                        'locationy', 
                        'hp', 
                        'direction', 
                        'action',
                        'creatureid' 
                    )
                );
            }
        }
    }
?>
