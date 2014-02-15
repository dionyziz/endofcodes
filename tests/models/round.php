<?php
    require_once 'models/round.php';
    require_once 'models/game.php';

    class RoundTest extends UnitTestWithFixtures {
        public function testSaveDb() {
            $game = $this->buildGame();
            $game->rounds[ 0 ] = $round = $this->buildRound();
            $round->game = $game;
            $round->save();
            $dbRound = new Round( $game, 1 );

            $this->assertEquals( $round->id, intval( $dbRound->id ), "Round's id must be correctly stored in the database" );
            $this->assertEquals( $round->game->id, intval( $dbRound->game->id ), "Round's gameid must be correctly stored in the database" );

            foreach ( $dbRound->creatures as $i => $dbCreature ) {
                $creature = $round->creatures[ $i ];

                $this->assertEquals( $creature->id, intval( $dbCreature->id ), "Creature's id must be correctly stored in the database" );
                $this->assertEquals( $creature->locationx, intval( $dbCreature->locationx ), "Creature's locationx must be correctly stored in the database" );
                $this->assertEquals( $creature->locationy, intval( $dbCreature->locationy ), "Creature's locationy must be correctly stored in the database" );
                $this->assertEquals( $creature->hp, intval( $dbCreature->hp ), "Creature's hp must be correctly stored in the database" );
                $this->assertTrue( isset( $dbCreature->user ), "Creature must have a user" );
                $this->assertEquals( $creature->user->id, intval( $dbCreature->user->id ), "Creature's userid must be correctly stored in the database" );
            }
        }
    }
    return new RoundTest();
?>
