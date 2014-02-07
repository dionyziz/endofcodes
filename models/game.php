<?php
    define( 'MIN_CREATURES', 100 );
    define( 'MAX_CREATURES', 199 );
    define( 'MIN_MULTIPLIER', 3 );
    define( 'MAX_MULTIPLIER', 4 );
    define( 'MIN_HP', 100 );
    define( 'MAX_HP', 199 );

    class Game extends ActiveRecordBase {
        public $id;
        public $created;
        public $width;
        public $height;
        public $rounds = [];
        public $users;
        public $creaturesPerPlayer;
        public $maxHp;
        public $grid = [ [] ];
        protected $tableName = 'games';
        protected $attributes = [ 'width', 'height', 'created' ];

        public function __construct( $id = false ) {
            if ( $id ) {
                $this->exists = true;
                $game_info = dbSelectOne( 'games', [ 'created', 'width', 'height' ], compact( 'id' ) );
                $this->id = $gameid = $id;
                $this->created = $game_info[ 'created' ];
                $this->width = $game_info[ 'width' ];
                $this->height = $game_info[ 'height' ];
                $rounds = dbSelect( 'roundcreatures', [ 'roundid' ], compact( 'gameid' ) );
                for ( $i = 0; $i < count( $rounds ); ++$i ) {
                    $this->rounds[ $i ] = new Round( $this, $i );
                }
            }
            else {
                $this->rounds = [];
            }
        }

        protected function onBeforeCreate() {
            $this->creaturesPerPlayer = rand( MIN_CREATURES, MAX_CREATURES );
            $multiply = $this->creaturesPerPlayer * count( $this->users );
            $this->width = rand( MIN_MULTIPLIER * $multiply + 1, MAX_MULTIPLIER * $multiply - 1 );
            $this->height = rand( MIN_MULTIPLIER * $multiply + 1, MAX_MULTIPLIER * $multiply - 1 );
            $this->maxHp = rand( MIN_HP, MAX_HP );
            $this->created = date( 'Y-m-d H:i:s' );
        }

        public function genesis() {
            $this->rounds[ 0 ] = new Round();
            $id = 0;
            foreach ( $this->users as $user ) {
                for ( $j = 0; $j < $this->creaturesPerPlayer; ++$j, ++$id ) {
                    $creature = new Creature();
                    $creature->id = $id;
                    $creature->user = $user;
                    $creature->round = $this->rounds[ 0 ];
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
                    $this->rounds[ 0 ]->creatures[] = $creature;
                }
            }
        }

        public function botError( $round, $user, $error ) {
            if ( !isset( $round->errors[ $user->id ] ) ) {
                $round->errors[ $user->id ] = [];
            }
            $round->errors[ $user->id ][] = $error;
        }

        protected function killBot( $user, $error ) {
            $roundid = count( $this->rounds ) - 1;
            foreach ( $this->rounds[ $roundid ]->creatures as $creature ) {
                if ( $creature->user->id === $user->id ) {
                    $creature->kill();
                }
            }
            $this->botError( $this->rounds[ $roundid ], $user, $error );
        }

        public function nextRound() {
            include_once 'models/resolution.php';
            $roundid = count( $this->rounds );
            $this->rounds[ $roundid ] = new Round( $this->rounds[ $roundid - 1 ] );
            $currentRound = $this->rounds[ $roundid ];
            foreach ( $currentRound->creatures as $creature ) {
                if ( $creature->intent->action === ACTION_ATTACK ) {
                    if ( $creature->alive ) {
                        creatureAttack( $creature );
                    }
                    else {
                        $roundNumber = count( $this->rounds ) - 1;
                        $this->killBot(
                            $creature->user,
                            "Tried to move dead creature $creature->id which " .
                                "was at location ($creature->locationx, $creature->locationy) " .
                                "to direction " . directionConstToString( $creature->intent->direction ) . " on round $roundNumber."
                        );
                    }
                }
            }
            foreach ( $currentRound->creatures as $creature ) {
                if ( $creature->intent->action === ACTION_MOVE ) {
                    if ( $creature->alive ) {
                        creatureMove( $creature );
                    }
                    else {
                        $roundNumber = count( $this->rounds ) - 1;
                        $this->killBot(
                            $creature->user,
                            "Tried to attack with dead creature $creature->id which " .
                                "was at location ($creature->locationx, $creature->locationy) " .
                                "to direction " . directionConstToString( $creature->intent->direction ) . " on round $roundNumber."
                        );
                    }
                }
            }
            foreach ( $currentRound->creatures as $creature ) {
                if ( $creature->hp <= 0 ) {
                    $creature->alive = false;
                }
            }
            $creatureLocation = [];
            foreach ( $currentRound->creatures as $creature ) {
                if ( $creature->alive ) {
                    if ( !isset( $creatureLocation[ $creature->locationx ] ) ) {
                        $creatureLocation[ $creature->locationx ] = [];
                    }
                    if ( !isset( $creatureLocation[ $creature->locationx ][ $creature->locationy ] ) ) {
                        $creatureLocation[ $creature->locationx ][ $creature->locationy ] = [];
                    }
                    $creatureLocation[ $creature->locationx ][ $creature->locationy ][] = $creature;
                }
            }
            $finished = false;
            while ( !$finished ) {
                $finished = true;
                for ( $i = 0; $i < $this->width; ++$i ) {
                    for ( $j = 0; $j < $this->height; ++$j ) {
                        if ( isset( $creatureLocation[ $i ][ $j ] ) && count( $creatureLocation[ $i ][ $j ] ) > 1 ) {
                            $finished = false;
                            foreach ( $creatureLocation[ $i ][ $j ] as $key => $creature ) {
                                $prevCreature = $this->rounds[ $roundid - 1 ]->creatures[ $creature->id ];
                                if ( $prevCreature->locationx !== $i || $prevCreature->locationy !== $j ) {
                                    $creature->locationx = $prevCreature->locationx;
                                    $creature->locationy = $prevCreature->locationy;
                                    $creatureLocation[ $creature->locationx ][ $creature->locationy ][] = $creature;
                                    unset( $creatureLocation[ $i ][ $j ][ $key ] );
                                }
                            }
                        }
                    }
                }
            }
        }

        public function toJson() {
            return json_encode( $this->jsonSerialize() );
        }

        public function jsonSerialize() {
            $gameid = $this->id;
            $W = $this->width;
            $H = $this->height;
            $M = $this->creaturesPerPlayer;
            $MAX_HP = $this->maxHp;
            $players = [];
            foreach ( $this->users as $user ) {
                $players[] = $user->jsonSerialize();
            }
            return compact( 'gameid', 'W', 'H', 'M', 'MAX_HP', 'players' );
        }
    }

    class GameException extends Exception {}
    class CreatureOutOfBoundsException extends GameException {}
?>
