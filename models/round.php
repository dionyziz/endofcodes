<?php
    class Round extends ActiveRecordBase {
        public $creatures;
        public $id;
        public $gameid;

        protected function validate() {
            if ( !is_int( $this->id ) ) {
                throw new ModelValidationException( 'id_not_valid' );
            }
            if ( !is_int( $this->gameid ) ) {
                throw new ModelValidationException( 'gameid_not_valid' );
            }
        }

        protected function create() {
            $gameid = $this->gameid;
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
