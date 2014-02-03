<?php
    include_once 'models/round.php';
    class RoundTest extends UnitTestWithUser {
        protected function buildRound() {
            $round = new Round();
            $round->id = 1;
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->locationx = $creature1->locationy = 1;
            $creature2->locationx = $creature2->locationy = 2;
            $creature1->hp = $creature2->hp = 10;
            $creature1->user = $creature2->user = $this->buildUser( 'vitsalis' );
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

            $this->assertTrue( isset( $data->round ), 'Roundid must exist in exported JSON' ); 
            $this->assertEquals( $round->id, $data->round, 'roundid must be encoded properly to JSON' );

            $this->assertTrue( isset( $data->map ), 'Map must exist in exported JSON' );
            $this->assertTrue( is_array( $data->map ), 'Map must be an array in exported JSON' );
            $this->assertEquals( 2, count( $data->map ), 'Map must contain correct number of creatures in exported JSON' );

            $this->assertEquals( 1, $data->map[ 0 ]->creatureid, 'All creatures must exist in exported JSON' );
            $this->assertEquals( 2, $data->map[ 1 ]->creatureid, 'All creatures must exist in exported JSON' );
        }
    }
    return new RoundTest();
?>
