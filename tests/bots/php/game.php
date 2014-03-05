<?php
    class BotTest extends UnitTestWithFixtures {
        public function testSendGameRequest() {
            ob_start();
            include 'bots/php/game.php';
            $response = json_decode( ob_get_clean() );

            $this->assertTrue( $response !== false, 'The game request response must have valid json' );

            $this->assertTrue( is_array( $response ), 'The game request response must be an array' );
            $this->assertTrue( empty( $response ), 'The game request response must be an empty array' );
        }
    }
    return new BotTest();
?>
