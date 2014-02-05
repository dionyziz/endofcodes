<?php
    include_once 'models/round.php';

    class RoundTest extends UnitTestWithFixtures {
        protected function buildRound() {
            $round = new Round();
            $round->id = 1;
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->locationx = 1;
            $creature1->locationy = 2;
            $creature2->locationx = 3;
            $creature2->locationy = 4;
            $creature1->hp = 10;
            $creature2->hp = 11;
            $creature1->user = $this->buildUser( 'vitsalis' );
            $creature2->user = $this->buildUser( 'pkakelas' );
            $creature1->id = 1;
            $creature2->id = 2;
            $round->creatures = array( $creature1, $creature2 );
            return $round;
        }
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
    }
    return new RoundTest();
?>
