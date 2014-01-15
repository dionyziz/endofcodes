<?php
    class Round extends ActiveRecordBase {
        public $creatures;
        public $id;
        public $game;

        public function __construct( $game = false, $id = false ) {
            if ( $id && $game ) {
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
                    $this->creatures[ $i ] = new Creature( $creature_info );
                    $this->creatures[ $i ]->game = $game;
                    $this->creatures[ $i ]->round = $this;
                }
            }
        }

        protected function validate() {
            if ( !is_int( $this->id ) ) {
                throw new ModelValidationException( 'id_not_valid' );
            }
            if ( !is_int( $this->game->id ) ) {
                throw new ModelValidationException( 'gameid_not_valid' );
            }
        }

        protected function create() {
            $gameid = $this->game->id;
            $roundid = $this->id;
            foreach ( $this->creatures as $creature ) {
                    $locationx = $creature->x;
                    $locationy = $creature->y;
                    $hp = $creature->hp;
                    switch ( $creature->intent->direction ) {
                        case 0:
                            $desire = 'NONE';
                            break;
                        case 1:
                            $desire = 'NORTH';
                            break;
                        case 2:
                            $desire = 'EAST';
                            break;
                        case 3:
                            $desire = 'SOUTH';
                            break;
                        case 4:
                            $desire = 'WEST';
                            break;
                    }
                    $creatureid = $creature->id;
                    dbInsert(
                        'roundcreatures',
                        compact( 
                            'gameid', 
                            'roundid', 
                            'locationx', 
                            'locationy', 
                            'hp', 
                            'desire', 
                            'creatureid' 
                        )
                    );
            }
        }
    }
?>
