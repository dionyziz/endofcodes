<?php
    include_once 'models/game.php';
    include_once 'models/creature.php';
    include_once 'models/round.php';
    include_once 'models/intent.php';
    include_once 'models/user.php';

    class GameTest extends UnitTestWithUser {
        public function testInit() {
            $game = new Game();
            for ( $i = 1; $i <= 4; ++$i ) {
                $game->users[] = $this->buildUser( $i );
            }
            $game->save();
            $this->assertTrue( $game->width > 0, 'A game with users must have width' );
            $this->assertTrue( $game->height > 0, 'A game with users must have height' );
            $this->assertTrue( 
                3 * count( $game->users ) * $game->creaturesPerPlayer < $game->width * $game->height,
                '3NM < WH must be true' 
            );
        }
        protected function buildGame() {
            $game = new Game();
            for ( $i = 1; $i <= 4; ++$i ) {
                $game->users[] = $this->buildUser( $i );
            }
            $game->save();
            return $game;
        }
        public function testInitiation() {
            $game = $this->buildGame();
            $dbGame = new Game( 1 );
            $this->assertEquals( $game->height, intval( $dbGame->height ), 'Height in the db must be the same as the height during creation' );
            $this->assertEquals( $game->width, intval( $dbGame->width ), 'Width in the db must be the same as the width during creation' );
            $this->assertEquals( $game->created, $dbGame->created, 'Created in the db must be the same as the created during creation' );
            $this->assertEquals( $game->id, intval( $dbGame->id ), 'Id in the db must be the same as the id during creation' );
        }
        public function testGenesis() {
            $game = $this->buildGame();
            $game->genesis();
            $this->assertEquals( count( $game->rounds ), 1, 'A round must be created during genesis' );
            $this->assertTrue( isset( $game->rounds[ 0 ] ), 'The genesis must have an index of 0' );
            $userCountCreatures = array();
            // start from 1 because user's id starts from 1
            for ( $i = 1; $i <= count( $game->users ); ++$i ) {
                $userCountCreatures[ $i ] = 0;
            }
            $creatureCount = 0;
            for ( $i = 0; $i < $game->width; ++$i ) {
                for ( $j = 0; $j < $game->height; ++$j ) {
                    if ( isset( $game->grid[ $i ][ $j ] ) ) {
                        $creature = $game->grid[ $i ][ $j ];
                        ++$userCountCreatures[ $creature->user->id ];
                        $this->assertTrue( $creature->locationx >= 0, "A creature's x coordinate must be non-negative" );
                        $this->assertTrue( $creature->locationy >= 0, "A creature's y coordinate must be non-negative" );
                        $this->assertTrue( $creature->locationx < $game->width, "A creature's x coordinate must be inside the grid" );
                        $this->assertTrue( $creature->locationy < $game->height, "A creature's y coordinate must be inside the grid" );
                        ++$creatureCount;
                    }
                }
            }
            $this->assertEquals( 
                count( $game->users ) * $game->creaturesPerPlayer, 
                $creatureCount, 
                'Each player must have a certain number of creatures' 
            );
            $creaturesPerUser = $creatureCount / count( $game->users );
            foreach ( $userCountCreatures as $userCountCreature ) {
                $this->assertEquals( $userCountCreature, $creaturesPerUser, 'Each user must have the same number of creatures' );
            }
            foreach ( $game->rounds[ 0 ]->creatures as $creature ) {
                if ( isset( $grid[ $creature->locationx ][ $creature->locationy ] ) ) {
                    $this->assertFalse( $grid[ $creature->locationx ][ $creature->locationy ], 'Only one creature must exist per coordinate' );
                }
                else {
                    $grid[ $creature->locationx ][ $creature->locationy ] = true;
                }
            }
        }
        public function testResolution() {
            $game = $this->buildGame();
            $game->genesis();
            $prevRoundCount = count( $game->rounds );
            $roundid = count( $game->rounds );
            $game->rounds[ $roundid ] = new Round();
            $roundCount = count( $game->rounds );
            $this->assertEquals( $roundCount, $prevRoundCount + 1, 'A new round must be created' );
            foreach ( $round->creatures as $creature ) {
                if ( $creature->hp === 0 ) {
                    $this->assertFale( $creature->alive, 'A creature with 0 hp must not be alive' );
                }
                $this->assertTrue( $creature->locationx < $game->width && $creature->locationx >= 0, 'A creature must be inside the grid' );
                $this->assertTrue( $creature->locationy < $game->height && $creature->locationy >= 0, 'A creature must be inside the grid' );
            }
        }
    }

    return new GameTest();
?>
