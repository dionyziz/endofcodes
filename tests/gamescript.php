<?php
    require_once 'models/file.php';
    class GamescriptTest extends FunctionalTest {
        protected $mockPath = 'bots/mock';
        protected $currentPath;

        public function setUp() {
            $this->currentPath = getcwd();

            $mockPath = $this->mockPath;
            recursiveCopy( 'bots/php', $mockPath );
            file_put_contents( $mockPath . '/bot.php', str_replace( 'sample_username', 'sample_username2', file_get_contents( $mockPath . '/bot.php' ) ) );
        }
        public function testGamescript() {
            global $config;

            $this->buildUser( 'sample_username' );
            $this->buildUser( 'sample_username2', '', $config[ 'base' ] . $this->mockPath );
            exec( "ENVIRONMENT=test ./gamescript.sh", $output );
            $this->assertDoesNotThrow( function() {
                $lastGame = Game::getLastGame();
                $this->assertTrue( $lastGame->ended, 'The game must be finished' );
            }, 'ModelNotFoundException', 'A game must be created' );
        }
        public function testRunOnDifferentDirectory() {
            chdir( '../..' );
            exec( "ENVIRONMENT=test $this->currentPath/gamescript.sh", $output );
            chdir( $this->currentPath );
            $this->assertDoesNotThrow( function() {
                Game::getLastGame();
            }, 'ModelNotFoundException', 'A game must be created' );
        }
        public function tearDown() {
            chdir( $this->currentPath );
            $this->rrmdir( 'bots/mock' );
        }
    }
    return new GamescriptTest();
?>
