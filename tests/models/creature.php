<?php
    require_once 'models/creature.php';

    class CreatureTest extends UnitTestWithFixtures {
        protected function buildCreature() {
            $creature = new Creature();
            $creature->user = $this->buildUser( 'dionyziz' );
            $creature->id = 1;
            $creature->locationx = 1;
            $creature->locationy = 2;
            $creature->hp = 10;
            $game = new Game();
            $game->save();
            $creature->game = $game;

            return $creature;
        }
        public function testJsonSerialize() {
            $creature = $this->buildCreature();
            $this->assertTrue( method_exists( $creature, 'toJson' ), 'Creature object must export a "toJson" function' );
            $json = $creature->toJson();
            $data = json_decode( $json );

            $this->assertTrue( isset( $data->userid ), 'userid must exist in encoded JSON' );
            $this->assertTrue( isset( $data->hp ), 'hp must exist in encoded JSON' );
            $this->assertTrue( isset( $data->x ), 'x must exist in encoded JSON' );
            $this->assertTrue( isset( $data->y ), 'y must exist in encoded JSON' );
            $this->assertTrue( isset( $data->creatureid ), 'creatureid must exist in encoded JSON' );

            $this->assertEquals( $creature->user->id, $data->userid, 'userid must be encoded properly to JSON' );
            $this->assertEquals( $creature->hp, $data->hp, 'hp must be encoded properly to JSON' );
            $this->assertEquals( $creature->locationx, $data->x, 'x must be encoded properly to JSON' );
            $this->assertEquals( $creature->locationy, $data->y, 'y must be encoded properly to JSON' );
            $this->assertEquals( $creature->id, $data->creatureid, 'creatureid must be encoded properly to JSON' );
        }
        public function testSaveCreatureDb() {
            $creature = $this->buildCreature();
            $creature->save();
            $caught = false;
            try {
                $dbCreature = new Creature( $creature->id, $creature->user->id, $creature->game->id );
            }
            catch ( ModelNotFoundException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, 'A creature must be stored in the database' );

            $this->assertSame( $creature->id, $dbCreature->id, 'A creatures id must be correctly stored in the database' );
            $this->assertSame( $creature->user->id, $dbCreature->user->id, 'A creatures userid must be correctly stored in the database' );
            $this->assertSame( $creature->game->id, $dbCreature->game->id, 'A creatures gameid must be correctly stored in the database' );
        }
    }
    return new CreatureTest();
?>
