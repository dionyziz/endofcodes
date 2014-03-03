<?php
    require_once 'models/creature.php';

    class CreatureTest extends UnitTestWithFixtures {
        public function testJsonSerialize() {
            $creature = $this->buildCreature( 1, 1, 1, $this->buildUser( 'vitsalis' ), $this->buildGame() );
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
            $creature = $this->buildCreature( 1, 1, 1, $this->buildUser( 'vitsalis' ), $this->buildGame() );
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
        public function testSaveMulti() {
            $user = $this->buildUser( 'vitsalis' );
            $game = $this->buildGame();
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 1;
            $creature2->id = 2;
            $creature1->user = $creature2->user = $user;
            $creature1->game = $creature2->game = $game;
            Creature::saveMulti( [ 1 => $creature1, 2 => $creature2  ] );

            $dbCreatures = dbSelect( 'creatures' );

            $this->assertEquals( 2, count( $dbCreatures ) );
            $dbCreature1 = $dbCreatures[ 0 ];
            $this->assertSame( $creature1->id, $dbCreature1[ 'id' ], "saveMulti must insert the values specified" );
            $this->assertSame( $creature1->user->id, $dbCreature1[ 'userid' ], "saveMulti must insert the values specified" );
            $this->assertSame( $creature1->game->id, $dbCreature1[ 'gameid' ], "saveMulti must insert the values specified" );
            $dbCreature2 = $dbCreatures[ 1 ];
            $this->assertSame( $creature2->id, $dbCreature2[ 'id' ], "saveMulti must insert the values specified" );
            $this->assertSame( $creature2->user->id, $dbCreature2[ 'userid' ], "saveMulti must insert the values specified" );
            $this->assertSame( $creature2->game->id, $dbCreature2[ 'gameid' ], "saveMulti must insert the values specified" );
        }
    }
    return new CreatureTest();
?>
