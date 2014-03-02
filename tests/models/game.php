<?php
    require_once 'models/game.php';
    require_once 'models/creature.php';
    require_once 'models/round.php';
    require_once 'models/intent.php';
    require_once 'models/user.php';

    class GameTest extends UnitTestWithFixtures {
        public function testInit() {
            $game = new Game();
            for ( $i = 1; $i <= 4; ++$i ) {
                $game->users[] = $this->buildUser( $i );
            }
            $game->save();
            $game->initiateAttributes();
            $this->assertTrue( $game->width > 0, 'A game with users must have width' );
            $this->assertTrue( $game->height > 0, 'A game with users must have height' );
            $this->assertTrue(
                3 * count( $game->users ) * $game->creaturesPerPlayer < $game->width * $game->height,
                '3NM < WH must be true'
            );
        }
        public function testSaveInDb() {
            $game = $this->buildGame();

            $dbGame = new Game( 1 );

            $this->assertSame( $game->id, $dbGame->id, "Game's id must be correctly stored in the database" );
            $this->assertSame( $game->width, $dbGame->width, "Game's width must be correctly stored in the database" );
            $this->assertSame( $game->height, $dbGame->height, "Game's height must be correctly stored in the database" );
            $this->assertSame( $game->created, $dbGame->created, "Game's created must be correctly stored in the database" );
        }
        public function testRetrieveRound() {
            $game = $this->buildGame();
            $round = new Round();
            $round->id = 0;
            $round->game = $game;
            $round->creatures = [
                1 => $this->buildCreature( 1, 0, 0, $game->users[ 1 ] ),
                2 => $this->buildCreature( 2, 1, 1, $game->users[ 2 ] )
            ];
            $round->save();
            $game->rounds = [ $round ];

            $dbGame = new Game( 1 );

            $this->assertTrue( isset( $dbGame->users ), 'Constructor of game must find the users' );
            $this->assertEquals( 2, count( $dbGame->users ), 'Game users must be retrieved' );

            $this->assertSame( $game->users[ 1 ]->id, $dbGame->users[ 1 ]->id, 'Constructor of the game must find the actual users' );
            $this->assertSame( $game->users[ 2 ]->id, $dbGame->users[ 2 ]->id, 'Constructor of the game must find the actual users' );

            $this->assertTrue( isset( $dbGame->rounds ), 'Constructor of game must find the rounds' );
            $this->assertEquals( 1, count( $dbGame->rounds ), 'Game rounds must be retrieved' );
        }
        public function testInitiation() {
            $game = $this->buildGame();
            $dbGame = new Game( 1 );
            $this->assertSame( $game->height, $dbGame->height, 'Height in the db must be the same as the height during creation' );
            $this->assertSame( $game->width, $dbGame->width, 'Width in the db must be the same as the width during creation' );
            $this->assertEquals( $game->created, $dbGame->created, 'Created in the db must be the same as the created during creation' );
            $this->assertSame( $game->id, $dbGame->id, 'Id in the db must be the same as the id during creation' );
        }
        public function testGenesis() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();
            $this->assertEquals( count( $game->rounds ), 1, 'A round must be created during genesis' );
            $this->assertTrue( isset( $game->rounds[ 0 ] ), 'The genesis must have an index of 0' );

            $caught = false;
            try {
                $round = new Round( $game, 0 );
            }
            catch ( ModelNotFoundException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, 'A round must be created in the database after genesis' );

            $userCountCreatures = [];
            // start from 1 because user's id starts from 1
            for ( $i = 1; $i <= count( $game->users ); ++$i ) {
                $userCountCreatures[ $i ] = 0;
            }
            $creatureCount = 0;
            $creatures = [];
            for ( $i = 0; $i < $game->width; ++$i ) {
                for ( $j = 0; $j < $game->height; ++$j ) {
                    if ( isset( $game->grid[ $i ][ $j ] ) ) {
                        $creature = $game->grid[ $i ][ $j ];
                        $creatures[] = $game->grid[ $i ][ $j ];
                        ++$userCountCreatures[ $creature->user->id ];
                        $this->assertTrue( $creature->id >= 1, "Creatures ids must start from 1" );
                        $this->assertTrue( $creature->locationx >= 0, "A creature's x coordinate must be non-negative" );
                        $this->assertTrue( $creature->locationy >= 0, "A creature's y coordinate must be non-negative" );
                        $this->assertTrue( $creature->locationx < $game->width, "A creature's x coordinate must be inside the grid" );
                        $this->assertTrue( $creature->locationy < $game->height, "A creature's y coordinate must be inside the grid" );
                        ++$creatureCount;
                    }
                }
            }
            $caught = false;
            try {
                Creature::selectUseridMulti( $creatures );
            }
            catch ( ModelNotFoundException $e ) {
                $caught = true;
            }
            $this->assertFalse( $caught, 'All the creatures must be stored in the database' );
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
        public function testKillBot() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();

            $this->assertTrue( method_exists( $game, "killBot" ), 'Game object must export a killBot function' ); 
            $game->killBot( $game->users[ 1 ], 'fuck him' );

            foreach ( $game->rounds[ 0 ]->creatures as $creature ) {
                if ( $creature->user->id === $game->users[ 1 ]->id ) {
                    $this->assertFalse( $creature->alive, 'killBot must kill all the creatures of a user' );
                    $this->assertEquals( 0, $creature->hp, 'Dead creatures must have 0 hp' );
                    $this->assertEquals( ACTION_NONE, $creature->intent->action, 'Dead creature must have action set to none' );
                    $this->assertEquals( DIRECTION_NONE, $creature->intent->direction, 'Dead creature must have direction set to none' );
                }
            }
        }
        public function testGameIdNonZero() {
            $game = $this->buildGame();

            $this->assertEquals( 1, $game->id, 'Game id must be 1 when the first game is created' );
        }
    }

    return new GameTest();
?>
