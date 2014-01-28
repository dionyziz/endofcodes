<?php
    class Round extends ActiveRecordBase {
        public $creatures;
        public $id;
        public $game;

        public function __construct( $game = false, $id = false ) {
            if ( $id !== false && $game !== false ) {
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
            else {
                $this->creatures = array();
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
