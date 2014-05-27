<?php
    class GamescriptTest extends FunctionalTest {
        public function testGamescript() {
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
