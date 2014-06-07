<?php
    require_once 'helpers/copy.php';
    class GamescriptTest extends FunctionalTest {
        public function testGamescript() {
            global $config;

            $copyName = $config[ 'base' ] . 'bots/mock';
            recurse_copy( $config[ 'base' ] . 'bots/php', $copyName );
            file_put_contents( $copyName, str_replace( 'sample_username', 'sample_username2', file_get_contents( $copyName ) );
            $this->buildUser( 'sample_username' );
            $this->buildUser( 'sample_username2', $copyName );
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
