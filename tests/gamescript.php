<?php
    class GamescriptTest extends FunctionalTest {
        public function testGamescript() {
            global $config;

            $user = new User();
            $user->username = 'sample_username';
            $user->email = 'sample@gmail.com';
            $user->password = 'secret1234';
            $user->boturl = $config[ 'base' ] . 'bots/php';
            $user->save();
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
