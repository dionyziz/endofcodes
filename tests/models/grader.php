<?php
    include_once 'models/grader.php';
    include_once 'models/game.php';
    include_once 'models/round.php';

    class CurlConnectionMock implements CurlConnectionInterface {
        public $url;
        public $data;
        public $requestMethod;
        public $executed = false;

        public function __construct() {
        }
        public function setOpt( $option, $value ) {
            switch ( $option ) {
                case CURLOPT_URL:
                    $this->url = $value;
                    break;
                case CURLOPT_POST:
                    $this->requestMethod = 'POST';
                    break;
                case CURLOPT_POSTFIELDS:
                    $this->data = $value;
                    break;
            }
        }
        public function exec() {
            $this->executed = true;
        }
        public function __destruct() {
        }
    }

    class GraderTest extends UnitTestWithFixtures {
        public function testInitiateRequest() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendInitiateRequest' ), 'GraderBot object must export a "sendInitiateRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $answer = $bot->sendInitiateRequest();

            $this->assertEquals( $botbase . '/bot', $curlConnectionMock->url, 'Initiation must send a request to the URL {{botbase}}/bot' );
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'Initiation must do a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'Initiation must execute curl request' );
        }
        public function testGameRequest() {
            $game = $this->buildGame();
            $game->genesis();
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendGameRequest' ), 'GraderBot object must export a "sendGameRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $answer = $bot->sendGameRequest( $game );

            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'GameRequest must be a POST request' );
            $this->assertEquals( $game->toJson(), $curlConnectionMock->data[ 0 ], 'GameRequest must send the correct JSON' );
            $this->assertTrue( $curlConnectionMock->executed, 'GameRequest must execute curl request' );
        }
        public function testRoundRequest() {
            $game = $this->buildGame();
            $game->genesis();
            $round = $game->rounds[ 0 ];
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendRoundRequest' ), 'GraderBot object must export a "sendRoundRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $answer = $bot->sendRoundRequest( $round );

            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'RoundRequest must be a POST request' );
            $this->assertEquals( $round->toJson(), $curlConnectionMock->data[ 0 ], 'RoundRequest must send the correct JSON' );
        }
    }

    return new GraderTest();
?>
