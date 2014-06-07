<?php
    require_once 'helpers/file.php';
    class GamescriptTest extends FunctionalTest {
        public function setUp() {
            $copyName = 'bots/mock';
            recurse_copy( 'bots/php', $copyName );
            file_put_contents( $copyName . '/bot.php', str_replace( 'sample_username', 'sample_username2', file_get_contents( $copyName . '/bot.php' ) ) );
        }
        public function tearDown() {
            recurse_delete( 'bots/mock' );
        }
        public function testGamescript() {
            global $config;

            $this->buildUser( 'sample_username' );
            $this->buildUser( 'sample_username2', $config[ 'base' ] . 'bots/mock' );
            exec( "ENVIRONMENT=test ./gamescript.sh", $output );
            $created = true;
            try {
                $lastGame = Game::getLastGame();
            }
            catch ( ModelNotFoundException $e ) {
                $created = false;
            }
            $this->assertTrue( $created, 'A game must be created' );
            $this->assertTrue( $lastGame->ended, 'The game must be finished' );
        }
    }
    
    return new GamescriptTest();
?>
