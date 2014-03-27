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
        protected function buildRoundWithCreatures( $users ) {
            $creatureid = 1;
            $x = $y = 0;
            $creatures = [];
            foreach ( $users as $user ) {
                $creatures[ $creatureid ] = $this->buildCreature( $creatureid, $x, $y, $user );
                ++$x;
                ++$y;
                ++$creatureid;
            }
            $round = new Round();
            $round->creatures = $creatures;
            return $round;
        }
        public function testIsFinalRoundMultipleCreaturesDifferentUsers() {
            $user1 = $this->buildUser( 'vitsalis' );
            $user2 = $this->buildUser( 'dionyziz' );
            $users = [ $user1->id => $user1, $user2->id => $user2 ];
            $round = $this->buildRoundWithCreatures( $users );
            $this->assertTrue( method_exists( $round, 'isFinalRound' ), 'Round object must export an isFinalRound function' );
            $this->assertFalse( $round->isFinalRound(), "isFinalRound() must be false if the round's creatures belong to multiple users" );
        }
        public function testIsFinalRound() {
            $user1 = $this->buildUser( 'vitsalis' );
            $users = [ $user1->id => $user1 ];
            $round = $this->buildRoundWithCreatures( $users );
            $this->assertTrue( $round->isFinalRound(), "isFinalRound() must be true if the round's creatures belong to only one user" );
        }
        public function testIsFinalRoundNoCreatures() {
            $round = $this->buildRoundWithCreatures( [] );
            $this->assertTrue( $round->isFinalRound(), "isFinalRound() must be true if the round has no creatures" );
        }
        public function testGetWinnerIdOneUser() {
            $user1 = $this->buildUser( 'vitsalis' );
            $users = [ $user1->id => $user1 ];
            $round = $this->buildRoundWithCreatures( $users );
            $this->assertEquals( $user1->id, $round->getWinnerId(), 'Get winner id must return the id of the winner' );
        }
        public function testGetWinnerIdNoUsers() {
            $round = $this->buildRoundWithCreatures( [] );
            $this->assertFalse( $round->getWinnerId(), 'If there are no players in the round, getWinnerId must return false' );
        }
        public function testWinnerIdMultipleUsers() {
            $user1 = $this->buildUser( 'vitsalis' );
            $user2 = $this->buildUser( 'dionyziz' );
            $users = [ $user1->id => $user1, $user2->id => $user2 ];
            $round = $this->buildRoundWithCreatures( $users );
            $this->assertThrows( [ $round, 'getWinnerId' ], 'ModelValidationException', 'A ModelValidationException must be thrown if the round is not the final one' );
        }
    }
    return new RoundTest();
?>
