<?php
    include_once 'models/game.php';
    include_once 'models/creature.php';
    include_once 'models/round.php';
    include_once 'models/intent.php';
    include_once 'models/user.php';

    class GameTest extends UnitTest {
        public function testInit() {
            $game = new Game();
            $game->usersCount = 10;
            $game->save();
            $this->assertEquals( $game->usersCount, 10, 'UsersCount must be the same as during creation' );
        }
        protected function buildGame() {
            $game = new Game();
            $game->usersCount = 10;
            $game->save();
            return $game;
        }
        public function testInitiation() {
            $game = $this->buildGame();
            $dbGame = new Game( 1 );
            $this->assertEquals( $game->height, intval( $dbGame->height ), 'Height in the db must be the same as the height during creation' );
            $this->assertEquals( $game->width, intval( $dbGame->width ), 'Width in the db must be the same as the width during creation' );
            $this->assertEquals( $game->created, $dbGame->created, 'Created in the db must be the same as the created during creation' );
            $this->assertEquals( $game->id, intval( $dbGame->id ), 'Id in the db must be the same as the id during creation' );
        }
    }

    return new GameTest();
?>
