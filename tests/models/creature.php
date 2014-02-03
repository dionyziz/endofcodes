<?php
    include_once 'models/creature.php';
    class CreatureTest extends UnitTestWithUser {
        protected function buildCreature() {
            $creature = new Creature();
            $creature->user = $this->buildUser( 'dionyziz' );
            $creature->id = 1;
            $creature->locationx = $creature->locationy = 1;
            $creature->hp = 10;
        }
        public function testJsonSerialize() {
            $creature = $this->buildCreature();
            $this->assertTrue( method_exists( $creature, 'toJson' ), 'Creature object must export a "toJson" function' );
            $json = $creature->toJson();
            $data = json_decode( $json );

            $this->assertTrue( isset( $data[ 'userid' ] ), 'userid must exist in encoded JSON' );
            $this->assertTrue( isset( $data[ 'hp' ] ), 'hp must exist in encoded JSON' );
            $this->assertTrue( isset( $data[ 'x' ] ), 'x must exist in encoded JSON' );
            $this->assertTrue( isset( $data[ 'y' ] ), 'y must exist in encoded JSON' );
            $this->assertTrue( isset( $data[ 'creatureid' ] ), 'creatureid must exist in encoded JSON' );

            $this->assertEquals( $creature->user->id, $data[ 'userid' ], 'Userid must be encoded properly to JSON' );
            $this->assertEquals( $creature->hp, $data[ 'hp' ], 'Hp must be encoded properly to JSON' );
            $this->assertEquals( $creature->locationx, $data[ 'x' ], 'x must be encoded properly to JSON' );
            $this->assertEquals( $creature->locationy, $data[ 'y' ], 'y must be encoded properly to JSON' );
            $this->assertEquals( $creature->id, $data[ 'creatureid' ], 'creatureid must be encoded properly to JSON' );
        }
    }
    return new CreatureTest();
?>