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
    }
    return new RoundTest();
?>
