<?php
    include_once 'models/game.php';
    include_once 'models/creature.php';
    include_once 'models/round.php';
    include_once 'models/intent.php';
    include_once 'models/user.php';

    class ResolutionTest extends UnitTestWithUser {
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
                $newCreature = clone $game->rounds[ $i + 1 ]->creatures[ 0 ];
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
            $creature1->game = $creature2->game = $game;
            $creature1->round = $creature2->round = $game->rounds[ 0 ];
            $creature1->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $creature2->intent = new Intent( ACTION_MOVE, DIRECTION_WEST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = array( $newCreature1, $newCreature2 );
            $game->nextRound();
            $newCreature1 = clone $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = clone $game->rounds[ 1 ]->creatures[ 1 ];
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
            $creature1->intent = new Intent( ACTION_ATACK, DIRECTION_SOUTH );
            $creature2->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = array( $newCreature1, $newCreature2 );
            $game->nextRound();
            $newCreature1 = clone $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = clone $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertEquals( $creature1->hp, $newCreature1->hp, 'An atacking creature must not lose hp' );
            $this->assertEquals( $creature2->hp, $newCreature2->hp + 1, 'A creature that has been atacked by a creature must lose 1 hp' );
            $this->assertTrue( $creature2->alive, 'A creature must not die if it still has hp' );
            $this->assertTrue( 
                $creature1->locationx === $newCreature1->locationx && $creature1->locationy === $newCreature1->locationy, 
                'An atacking creature must not change position' 
            );
        }
        public function testAttackAsVictimMoves() {
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
            $creature1->intent = new Intent( ACTION_ATACK, DIRECTION_SOUTH );
            $creature2->intent = new Intent( ACTION_MOVE, DIRECTION_EAST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $game->rounds[ 0 ]->creatures = array( $newCreature1, $newCreature2 );
            $game->nextRound();
            $newCreature1 = clone $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = clone $game->rounds[ 1 ]->creatures[ 1 ];
            $this->assertEquals( $creature2->hp, $newCreature2->hp + 1, 'A creature that has been atacked by a creature must lose 1 hp' );
            $this->assertTrue( 
                $creature2->locationx === $newCreature2->locationx - 1 && $creature2->locationy === $newCreature2->locationy, 
                'An atacked creature can move if it is still alive' 
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
            $creature1->intent = new Intent( ACTION_ATACK, DIRECTION_SOUTH );
            $creature2->intent = new Intent( ACTION_NONE, DIRECTION_NONE );
            $creature3->intent = new Intent( ACTION_ATACK, DIRECTION_WEST );
            $newCreature1 = clone $creature1;
            $newCreature2 = clone $creature2;
            $newCreature3 = clone $creature3;
            $game->rounds[ 0 ]->creatures = array( $newCreature1, $newCreature2, $newCreature3 );
            $game->nextRound();
            $newCreature1 = clone $game->rounds[ 1 ]->creatures[ 0 ];
            $newCreature2 = clone $game->rounds[ 1 ]->creatures[ 1 ];
            $newCreature3 = clone $game->rounds[ 1 ]->creatures[ 2 ];
            $this->assertEquals( $creature2->hp, $newCreature2->hp + 2, 'A creature that has been atacked by two creatures must lose 2 hp' );
        }
    }
    return new ResolutionTest();
?>
