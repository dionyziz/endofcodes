<?php
    define( 'MIN_CREATURES', 10 );
    define( 'MAX_CREATURES', 20 );
    define( 'MIN_MULTIPLIER', 3 );
    define( 'MAX_MULTIPLIER', 4 );
    define( 'MIN_HP', 1 );
    define( 'MAX_HP', 10 );

    class Game extends ActiveRecordBase {
        public $created;
        public $width;
        public $height;
        public $rounds = [];
        public $users = []; // dictionary from userid to user
        public $creaturesPerPlayer;
        public $maxHp;
        public $grid = [ [] ];
        public $attributesInitiated = false;
        public $ended = false;
        protected static $tableName = 'games';
        protected static $attributes = [ 'width', 'height', 'created' ];

        public static function getLastGame() {
            if ( !$game = dbSelect( 'games', [ 'id' ], [], 'created DESC', 1 ) ) {
                throw new ModelNotFoundException();
            }
            return new Game( $game[ 0 ][ 'id' ] );
        }

        public function __construct( $id = false ) {
            require_once 'models/round.php';
            if ( $id !== false ) {
                $this->exists = true;
                try {
                    $game_info = dbSelectOne( 'games', [ 'created', 'width', 'height' ], compact( 'id' ) );
                }
                catch ( DBException $e ) {
                    throw new ModelNotFoundException();
                }
                $this->id = $gameid = $id;
                $this->created = $game_info[ 'created' ];
                $this->width = $game_info[ 'width' ];
                $this->height = $game_info[ 'height' ];
                $data = dbSelectOne( 'roundcreatures', [ 'COUNT(DISTINCT roundid) AS countrounds' ], compact( 'gameid' ) );
                $countrounds = $data[ 'countrounds' ];
                if ( $countrounds > 0 ) {
                    for ( $i = 0; $i < $countrounds; ++$i ) {
                        $this->rounds[ $i ] = new Round( $this, $i );
                    }
                    foreach ( $this->rounds[ 0 ]->creatures as $creature ) {
                        $userid = $creature->user->id;
                        if ( !isset( $this->users[ $userid ] ) ) {
                            $this->users[ $userid ] = new User( $userid );
                        }
                    }
                    if ( end( $this->rounds )->isFinalRound() ) {
                        $this->ended = true;
                    }
                }
                else {
                    $this->ended = true;
                }
            }
            else {
                $this->rounds = [];
                $this->width = 0;
                $this->height = 0;
                $this->created = date( 'Y-m-d H:i:s' );
            }
        }

        public function initiateAttributes() {
            $this->creaturesPerPlayer = rand( MIN_CREATURES, MAX_CREATURES );
            $multiply = $this->creaturesPerPlayer * count( $this->users );
            $min = ceil( sqrt( MIN_MULTIPLIER * $multiply ) );
            $max = floor( sqrt( MAX_MULTIPLIER * $multiply ) );
            $this->width = rand( $min, $max );
            $this->height = rand( $min, $max );
            $this->maxHp = rand( MIN_HP, MAX_HP );
            $this->attributesInitiated = true;
        }

        public function getGlobalRatings() {
            $ratings = [];
            $found = [];

            for ( $i = count( $this->rounds ) - 1, $position = 1; $i >= 0; --$i ) {
                $newUsers = [];
                foreach ( $this->rounds[ $i ]->creatures as $creature ) {
                    if ( $creature->alive && !isset( $found[ $creature->user->id ] ) ) {
                        $newUsers[] = $creature->user;
                        $found[ $creature->user->id ] = true;
                    }
                }
                if ( !empty( $newUsers ) ) {
                    $ratings[ $position ] = $newUsers;
                    $position += count( $newUsers );
                }
            }
            return $ratings;
        }

        public function getCountryRatings( Country $country ) {
            $ratings = $this->getGlobalRatings();
            $countryRatings = [];

            foreach ( $ratings as $position => $users ) {
                $validUsers = [];
                foreach ( $users as $user ) {
                    if ( $user->country->id === $country->id ) {
                        $validUsers[] = $user;
                    }
                }
                $countryRatings[ $position ] = $validUsers;
            }
            return $countryRatings;
        }

        public function genesis() {
            assert( $this->attributesInitiated/*, 'game attributes not initiated before genesis'*/ );
            if ( count( $this->users ) === 0 ) {
                $this->ended = true;
                return;
            }

            $this->rounds[ 0 ] = new Round();
            $this->rounds[ 0 ]->game = $this;
            $this->rounds[ 0 ]->id = 0;
            $id = 1;
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
                    while ( true ) {
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
            Creature::saveMulti( $this->rounds[ 0 ]->creatures );
            $this->rounds[ 0 ]->save();
            $this->ended = $this->rounds[ 0 ]->isFinalRound();
        }

        public function killBot( $user, $description, $actual = '', $expected = '' ) {
            $round = $this->getCurrentRound();
            foreach ( $round->creatures as $creature ) {
                if ( $creature->user->id === $user->id ) {
                    $creature->kill();
                }
            }

            $this->getCurrentRound()->error( $user->id, $description, $actual, $expected );
        }

        public function nextRound() {
            require_once 'models/resolution.php';
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
                            "Tried to attack with dead creature $creature->id which " .
                                "was at location ($creature->locationx, $creature->locationy) " .
                                "to direction " . directionConstToString( $creature->intent->direction ) . " on round $roundNumber."
                        );
                    }
                }
            }
            foreach ( $currentRound->creatures as $creature ) {
                if ( $creature->intent->action === ACTION_MOVE ) {
                    if ( $creature->alive ) {
                        try {
                            creatureMove( $creature );
                        }
                        catch ( CreatureOutOfBoundsException $e ) {
                            $this->killBot(
                                $creature->user,
                                "Tried to move creature $creature->id out of map bounds."
                            );
                        }
                    }
                    else {
                        $roundNumber = count( $this->rounds ) - 1;
                        $this->killBot(
                            $creature->user,
                            "Tried to move with dead creature $creature->id which " .
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
            $this->rounds[ $roundid ]->save();
            $this->ended = $this->rounds[ $roundid ]->isFinalRound();
        }
        public function beforeNextRound() {
            foreach ( $this->getCurrentRound()->creatures as $creature ) {
                $creature->intent = new Intent();
            }
        }

        public function getCurrentRound() {
            return end( $this->rounds );
        }
    }

    class GameException extends Exception {
        public function __construct( $description ) {
            parent::__construct( "Game error: $description" );
        }
    }
?>
