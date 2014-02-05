<?php
    include_once 'models/round.php';
    include_once 'models/game.php';

    class RoundTest extends UnitTestWithFixtures {
        public function testJsonSerialize() {
            $round = $this->buildRound();

            $this->assertTrue( method_exists( $round, 'toJson' ), 'Round object must export a "toJson" function' );

            $json = $round->toJson();
            $data = json_decode( $json );

            $this->assertTrue( isset( $data->round ), 'roundid must exist in exported JSON' ); 
            $this->assertEquals( $round->id, $data->round, 'roundid must be encoded properly to JSON' );

            $this->assertTrue( isset( $data->map ), 'map must exist in exported JSON' );
            $this->assertTrue( is_array( $data->map ), 'map must be an array in exported JSON' );
            $this->assertEquals( 2, count( $data->map ), 'map must contain correct number of creatures in exported JSON' );

            $this->assertEquals( 1, $data->map[ 0 ]->creatureid, 'All creatures must exist in exported JSON' );
            $this->assertEquals( 2, $data->map[ 1 ]->creatureid, 'All creatures must exist in exported JSON' );
        }
        public function testSendJson() {
            $game = $this->buildGame();
            $game->genesis();
            $round = $game->rounds[ 0 ];

            $this->assertTrue( method_exists( $round, 'sendJson' ), 'Round object must export a "sendJson" function' );

            $json = $round->toJson();
            $outputs = $round->sendJson();

            $this->assertTrue( isset( $outputs ), 'sendJson must return the json it sent' );
            $this->assertTrue( is_array( $outputs ), 'sendJson must return an array' );
            $this->assertEquals( 4, count( $outputs ), 'sendJson must send json to all users' );

            foreach ( $outputs as $output ) {
                $this->assertEquals( $json, $output, 'sendJson must send the correct json to each user' );
            }
        }
    }
    return new RoundTest();
?>
