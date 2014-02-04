<?php
    include_once 'models/game.php';
    include_once 'models/creature.php';
    include_once 'models/round.php';
    include_once 'models/intent.php';
    include_once 'models/user.php';

    class ResolutionTest extends UnitTestWithFixtures {
        protected function buildGameWithUsers( $userCount ) {
            $game = new Game();
            for ( $i = 1; $i <= $userCount; ++$i ) {
                $game->users[] = $this->buildUser( $i ); 
            }
            $game->rounds[ 0 ] = new Round();
            $game->rounds[ 0 ]->id = 0;
            $game->rounds[ 0 ]->game = $game;
            $game->width = 10;
            $game->height = 10;
            return $game;
        }
        public function testMoveSingleCreature() {
            $game = $this->buildGameWithUsers( 1 );
            $creature = new Creature();
            $creature->id = 0;
            $creature->locationx = 2;
            $creature->locationy = 3;
            $creature->game = $game;
            $creature->round = $game->rounds[ 0 ];
            for ( $i = 0, $dir = 1; $i <= 3; ++$i, ++$dir ) {
                $creature->intent = new Intent( ACTION_MOVE, $dir );
                $newCreature = clone $creature;
                $game->rounds[ $i ]->creatures[ 0 ] = $newCreature;
                $game->nextRound();
                $newCreature = $game->rounds[ $i + 1 ]->creatures[ 0 ];
                switch ( $dir ) {
                    case DIRECTION_NORTH:
                        $this->assertEquals( $creature->locationx, $newCreature->locationx, 'A creature must not move to the in the x-direction when moving north' );
                        $this->assertEquals( $creature->locationy + 1, $newCreature->locationy, 'A creature must move up by 1 when moving north' );
                        break;
                    case DIRECTION_EAST:
                        $this->assertEquals( $creature->locationx + 1, $newCreature->locationx, 'A creature must move right by one when moving east' );
                        $this->assertEquals( $creature->locationy, $newCreature->locationy, 'A creature must not move to the y-direction when moving east' );
                        break;
                    case DIRECTION_SOUTH:
                        $this->assertEquals( $creature->locationx, $newCreature->locationx, 'A creature must not move to x-direction when moving south' );
                        $this->assertEquals( $creature->locationy - 1, $newCreature->locationy, 'A creature must move down by 1 when moving south' );
                        break;
                    case DIRECTION_WEST:
                        $this->assertEquals( $creature->locationx - 1, $newCreature->locationx, 'A creature must move left by one when moving west' );
                        $this->assertEquals( $creature->locationy, $newCreature->locationy, 'A creature must not move to the y-direction when moving west' );
                        break;
                }
            }
        }
        public function testMoveOutOfBounds() {
            $game = $this->buildGameWithUsers( 1 );
            for ( $dir = 1, $i = 0; $dir <= 4; ++$i, ++$dir ) {
                $creature = new Creature();
                $creature->id = 0;
                $creature->user = $game->users[ 0 ];
                if ( $dir <= 2 ) {
                    $creature->locationx = $creature->locationy = 9;
                }
                else {
                    $creature->locationx = $creature->locationy = 0;
                }
                $creature->intent = new Intent( ACTION_MOVE, $dir );
                $creature->hp = 10;
                $creature->game = $game;
                $creature->round = $game->rounds[ $i ];
                $newCreature = clone $creature;
                $game->rounds[ $i ]->creatures = [ $newCreature ];
                $game->nextRound();
                $newCreature = $game->rounds[ $i + 1 ]->creatures[ 0 ];
                $this->assertTrue(
                    $creature->locationx === $newCreature->locationx && $creature->locationy === $newCreature->locationy,
                    'A creature that tries to go out of bounds should remain to the same position'
                );
                $this->assertEquals( count( $game->rounds[ $i + 1 ]->errors[ $creature->user->id ] ), 1, 'A user must get an error if he tries to move a creature out of bounds' );
            }
        }
        public function testMoveDeadCreature() {
            $game = $this->buildGameWithUsers( 1 );
            $creature = new Creature();
            $creature->id = 0;
            $creature->user = $game->users[ 0 ];
            $creature->locationx = $creature->locationy = 8;
            $creature->hp = 0;
            $creature->alive = false;
            $creature->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $creature->game = $game;
            $creature->round = $game->rounds[ 0 ];
            $newCreature = clone $creature;
            $game->rounds[ 0 ]->creatures = [ $newCreature ];
            $game->nextRound();
            $newCreature = $game->rounds[ 1 ]->creatures[ 0 ];
            $this->assertTrue(
                $creature->locationx === $newCreature->locationx && $creature->locationy === $newCreature->locationy,
                'A dead creature can not move'
            );
            $this->assertEquals( count( $game->rounds[ 1 ]->errors[ $creature->user->id ] ), 1, 'A user must get an error if he tries to move a dead creature' );
        }
        public function testMoveOverlap() {
            $game = $this->buildGameWithUsers( 1 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = 2;
            $creature1->locationy = 1;
            $creature2->locationx = 3;
            $creature2->locationy = 2;
            $creature1->hp = $creature2->hp = 1;
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $creature2->intent = new Intent( ACTION_MOVE, DIRECTION_WEST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertTrue( 
                $newCreature1->locationx !== $newCreature2->locationx || $newCreature1->locationy !== $newCreature2->locationy, 
                'There can not be two creatures in the same location' 
            );
            $this->assertTrue( 
                $creature1->locationx === $newCreature1->locationx && $creature1->locationy === $newCreature1->locationy, 
                'When there are multiple creatures in one position all of them must return to the starting position' 
            );
            $this->assertTrue( 
                $creature2->locationx === $newCreature2->locationx && $creature2->locationy === $newCreature2->locationy, 
                'When there are multiple creatures in one position all of them must return to the starting position' 
            );
        }
        public function testAttackSingleCreature() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = 2;
            $creature1->locationy = 3;
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 10;
            $creature1->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertEquals( $creature1->hp, $newCreature1->hp, 'An attacking creature must not lose hp' );
            $this->assertEquals( $creature2->hp - 1, $newCreature2->hp, 'A creature that has been attacked by a creature must lose 1 hp' );
            $this->assertTrue( $creature2->alive, 'A creature must not die if it still has hp' );
            $this->assertTrue( 
                $creature1->locationx === $newCreature1->locationx && $creature1->locationy === $newCreature1->locationy, 
                'An attacking creature must not change position' 
            );
        }
        public function testAttackWithDeadCreature() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = 2;
            $creature1->locationy = 3;
            $creature1->alive = false;
            $creature1->hp = 0;
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature2->hp = 10;
            $creature1->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertEquals( 10, $newCreature2->hp, 'A creature that is being attacked by a dead creature must not lose hp' );
            $this->assertEquals( 1, count( $game->rounds[ 1 ]->errors[ $creature1->user->id ] ), 'A user that tries to attack with a dead creature must get an error' );
        }
        public function testAttackCreatureSameUser() {
            $game = $this->buildGameWithUsers( 1 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = 2;
            $creature1->locationy = 3;
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 10;
            $creature1->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 0 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertEquals( 10, $newCreature2->hp, 'A creature that is being attacked by a creature with the same user must not lose hp' );
            $this->assertEquals( 1, count( $game->rounds[ 1 ]->errors[ $creature1->user->id ] ), 'A user that tries to attack a creature of his own must get an error' );
        }
        public function testKillBot() {
            $game = $this->buildGameWithUsers( 1 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature2->id = 1;
            $creature2->user = $game->users[ 0 ];
            $creature2->locationx = $creature1->locationy = 8;
            $creature2->hp = 10;
            $creature2->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $creature2->game = $game;
            $creature2->round = $game->rounds[ 0 ];
            $creature1->id = 0;
            $creature1->user = $game->users[ 0 ];
            $creature1->locationx = $creature1->locationy = 8;
            $creature1->hp = 0;
            $creature1->alive = false;
            $creature1->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $creature1->game = $game;
            $creature1->round = $game->rounds[ 0 ];
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertFalse( $newCreature2->alive, "All of the user's creatures must die if he tries to move a dead creature" );
        }
        public function testAttackAsVictimMoves() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = 2;
            $creature1->locationy = 3;
            /* ACCII MAP:
                3. | - | - | 1 | - |
                2. | - | - | 2 | 3 |
                1. | - | - | - | - |
                0. | - | - | - | 3 |
                     0.  1.  2.  3. 
            */
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 10;
            $creature1->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $creature2->intent = new Intent( ACTION_MOVE, DIRECTION_EAST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertEquals( $creature2->hp - 1, $newCreature2->hp, 'A creature that has been attacked by a creature must lose 1 hp' );
            $this->assertTrue( 
                $creature2->locationx === $newCreature2->locationx - 1 && $creature2->locationy === $newCreature2->locationy, 
                'An attacked creature can move if it is still alive' 
            );
        }
        public function testAttackMultipleAttackersSingleVictim() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature3 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature3->id = 2;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = $creature3->locationy = 2;
            $creature1->locationy = $creature3->locationx = 3;
            $creature1->game = $creature2->game = $creature3->game = $game;
            $creature1->round = $creature2->round = $creature3->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 10;
            $creature1->user = $creature3->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $creature3->intent = new Intent( ACTION_ATTACK, DIRECTION_WEST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $newCreature3 = clone $creature3;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2, $newCreature3 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $newCreature3 = $game->rounds[ 1 ]->creatures[ 2 ];
            $this->assertEquals( $creature2->hp - 2, $newCreature2->hp, 'A creature that has been attacked by two creatures must lose 2 hp' );
        }
        public function testAttackAndKill() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = 2;
            $creature1->locationy = 3;
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 1;
            $creature1->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertFalse( $newCreature2->alive, 'A creature must die if it does not have any hp' );
        }
        public function testAttackAndKillWhileVictimMoves() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature1->locationx = $creature2->locationx = $creature2->locationy = 2;
            $creature1->locationy = 3;
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 1;
            $creature1->user = $game->users[ 0 ];
            $creature2->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_SOUTH );
            $creature2->intent = new Intent( ACTION_MOVE, DIRECTION_EAST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertFalse( $newCreature2->alive, 'A creature must die if it loses hp' );
            $this->assertTrue( $newCreature2->hp <= 0 && !$newCreature2->alive, 'A creature must die if it has less or equal to 0 hp' );
        }
        public function testAttackAndKillAttackingCreature() {
            $game = $this->buildGameWithUsers( 2 );
            $creature1 = new Creature();
            $creature2 = new Creature();
            $creature3 = new Creature();
            $creature1->id = 0;
            $creature2->id = 1;
            $creature3->id = 2;
            $creature1->locationx = 2;
            $creature1->locationy = $creature3->locationy = $creature3->locationx = $creature2->locationx = 3;
            $creature2->locationy = 4;
            /* ACCII MAP:
                4. | - | - | - | 2 |
                3. | - | - | 1 | 3 |
                2. | - | - | - | - |
                1. | - | - | - | - |
                0. | - | - | - | - |
                     0.  1.  2.  3. 
            */
            $creature1->game = $creature2->game = $creature3->game = $game;
            $creature1->round = $creature2->round = $creature3->round = $game->rounds[ 0 ];
            $creature1->hp = $creature2->hp = 10;
            $creature3->hp = 1;
            $creature1->user = $creature2->user = $game->users[ 0 ];
            $creature3->user = $game->users[ 1 ];
            $creature1->intent = new Intent( ACTION_ATTACK, DIRECTION_EAST );
            $creature3->intent = new Intent( ACTION_ATTACK, DIRECTION_NORTH );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $newCreature3 = clone $creature3;
            $game->rounds[ 0 ]->creatures = [ $newCreature1, $newCreature2, $newCreature3 ];
            $game->nextRound();
            $newCreature1 = $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 1 ];
            $newCreature3 = $game->rounds[ 1 ]->creatures[ 2 ];
            $this->assertFalse( $newCreature3->alive, 'A creature must die if it does not have hp' );
            $this->assertEquals( $creature2->hp - 1, $newCreature2->hp, 'A creature that is attacked before another creature dies must lose hp' );
        }
    }
    return new ResolutionTest();
?>
