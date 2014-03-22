<?php
    require_once 'models/round.php';
    require_once 'models/game.php';

    class RoundTest extends UnitTestWithFixtures {
        public function testSaveDb() {
            $game = $this->buildGame();
            $game->rounds[ 0 ] = $round = $this->buildRound();
            $round->game = $game;
            $round->save();
            $caught = false;
            try {
                $dbRound = new Round( $game, 1 );
            }
            catch ( ModelNotFoundException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, 'A round must be stored in the database' );

            $this->assertSame( $round->id, $dbRound->id, "Round's id must be correctly stored in the database" );
            $this->assertSame( $round->game->id, $dbRound->game->id, "Round's gameid must be correctly stored in the database" );

            foreach ( $dbRound->creatures as $id => $dbCreature ) {
                $creature = $round->creatures[ $id ];

                $this->assertSame( $creature->id, $dbCreature->id, "Creature's id must be correctly stored in the database" );
                $this->assertSame( $creature->locationx, $dbCreature->locationx, "Creature's locationx must be correctly stored in the database" );
                $this->assertSame( $creature->locationy, $dbCreature->locationy, "Creature's locationy must be correctly stored in the database" );
                $this->assertSame( $creature->hp, $dbCreature->hp, "Creature's hp must be correctly stored in the database" );
                $this->assertTrue( isset( $dbCreature->user ), "Creature must have a user" );
                $this->assertSame( $creature->user->id, $dbCreature->user->id, "Creature's userid must be correctly stored in the database" );
            }
        }
        public function testError() {
            $round = new Round();
            $round->error( 1, 'fuck this user', 'actual', 'expected' );

            $this->assertEquals( 'fuck this user', $round->errors[ 1 ][ 0 ][ 'description' ], 'description must store the description of the error specfied' );
            $this->assertEquals( 'expected', $round->errors[ 1 ][ 0 ][ 'expected' ], 'expected must store the expected of the error specfied' );
            $this->assertEquals( 'actual', $round->errors[ 1 ][ 0 ][ 'actual' ], 'actual must store the actual of the error specfied' );
        }
        public function testIsFinalRoundMultipleCreaturesDifferentUsers() {
            $user1 = $this->buildUser( 'vitsalis' );
            $user2 = $this->buildUser( 'dionyziz' );
            $creature1 = $this->buildCreature( 1, 0, 0, $user1 );
            $creature2 = $this->buildCreature( 2, 1, 1, $user2 );
            $round = new Round();
            $round->creatures = [ $creature1->id => $creature1, $creature2->id => $creature2 ];
            $this->assertTrue( method_exists( $round, 'isFinalRound' ), 'Round object must export an isFinalRound function' );
            $this->assertFalse( $round->isFinalRound(), "isFinalRound() must be false if the round's creatures belong to multiple users" );
        }
        public function testIsFinalRound() {
            $user = $this->buildUser( 'vitsalis' );
            $round = new Round();
            $creature = $this->buildCreature( 1, 0, 0, $user );
            $round->creatures = [ $creature->id => $creature ];
            $this->assertTrue( $round->isFinalRound(), "isFinalRound() must be true if the round's creatures belong to only one user" );
        }
        public function testIsFinalRoundNoCreatures() {
            $round = new Round();
            $round->creatures = [];
            $this->assertTrue( $round->isFinalRound(), "isFinalRound() must be true if the round has no creatures" );
        }
    }
    return new RoundTest();
?>
