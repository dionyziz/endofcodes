<?php
    require_once 'models/grader/bot.php';
    require_once 'models/grader/grader.php';
    require_once 'models/grader/serializer.php';
    require_once 'models/game.php';
    require_once 'models/round.php';
    require_once 'models/curl.php';

    class CurlConnectionMock implements CurlConnectionInterface {
        public $url;
        public $data;
        public $requestMethod;
        public $executed = false;
        public $response;
        protected $responseError;
        protected $hasResponseError;
        public $responseCode = 200;

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
            if ( $this->hasResponseError ) {
                throw new CurlException( $this->responseError );
            }
            $this->executed = true;
        }
        public function __destruct() {}
        public function makeRespondWith( $json ) {
            $this->response = $json;
        }
        public function makeRespondWithError( $errorno ) {
            $this->hasResponseError = true;
            $this->responseError = $errorno;
        }
        public function makeRespondWithErrorCode( $code ) {
            $this->responseCode = $code;
        }
    }

    class GraderBotTest extends UnitTestWithFixtures {
        public function testInitiateRequest() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendInitiateRequest' ), 'GraderBot object must export a "sendInitiateRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $curlConnectionMock->makeRespondWith( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'vitsalis'
            ] ) );
            $bot->sendInitiateRequest();

            $this->assertEquals( $botbase . '/bot', $curlConnectionMock->url, 'Initiation must send a request to the URL {{botbase}}/bot' );
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'Initiation must do a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'Initiation must execute curl request' );
        }
        public function testInitiateUsernameInvalid() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => $user->username
            ] ) );
            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            $this->assertFalse( $caught, 'A GraderBotException should not be thrown if the username is correct' );

            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'god'
            ] ) );

            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            $this->assertTrue( $caught, 'A GraderBotException should be thrown if username is incorrect' );

            $this->assertEquals( 1, count( $bot->errors ), 'Bot that replies with incorrect username should have an error reported' );
            $this->assertEquals( 'initiate_username_mismatch', $bot->errors[ 0 ], 'Bot that replies with incorrect username should have a "initiate_username_mismatch" error reported' );
        }
        protected function initiateAndGetErrors( $mock_error ) {
            $user = $this->buildUser( 'vitsalis' );
            $user->boturl = 'http://endofcodes.com/does_not_matter';
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;

            $curlConnectionMock->makeRespondWithError( $mock_error );

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testInitiateNotResolvedHostname() {
            $result = $this->initiateAndGetErrors( CURLE_COULDNT_RESOLVE_HOST );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'initiate_could_not_resolve', $result[ 'errors' ][ 0 ], 'Bot with url that could not be resolved must have a "initiate_could_not_resolve" error' );
        }
        public function testInitiateNetworkUnreachable() {
            $result = $this->initiateAndGetErrors( CURLE_COULDNT_CONNECT );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'initiate_could_not_connect', $result[ 'errors' ][ 0 ], 'Bot with url that could not be resolved must have a "initiate_could_not_connect" error' );
        }
        public function testIniatiateMalformedUrl() {
            $result = $this->initiateAndGetErrors( CURLE_URL_MALFORMAT );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught when curl responds with an error' );
            $this->assertEquals( 'initiate_malformed_url', $result[ 'errors' ][ 0 ], 'Bot with malformed url must have a "initiate_malformed_url" error' );
        }
        public function testInitiateRespondCodeInvalid() {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;

            $curlConnectionMock->makeRespondWithErrorCode( 404 );

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            $this->assertTrue( $caught, 'A GraderBotExcpetion must be caught when HTTP response code is not OK(200)' );
            $this->assertEquals( 'initiate_http_code_not_ok', $bot->errors[ 0 ], 'Bot whose HTTP response code is not OK(200) must have a "initiate_http_code_not_ok" error' );
        }
        public function testInitiateRespondValidJson() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'vitsalis'
            ] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if bot responds with valid json' );
            $this->assertTrue( empty( $result[ 'errors' ] ), 'There should be no errors if the json is valid' );
        }
        public function testInitiateRespondInvalidJson() {
            $result = $this->initiateWithJsonAndGetErrors( '{ invalid_json }' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response has invalid json' );
            $this->assertEquals( 'initiate_invalid_json', $result[ 'errors' ][ 0 ], 'Bot who has invalid json as a response must have a "initiate_invalid_json" error' );
        }
        protected function initiateWithJsonAndGetErrors( $json ) {
            $user = $this->buildUser( 'vitsalis' );
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );

            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendInitiateRequest();
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testInitiateRespondWithoutBotname() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'version' => '0.1.0',
                'username' => 'vitsalis'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response does not have a botname' );
            $this->assertEquals( 'initiate_botname_not_set', $result[ 'errors' ][ 0 ], 'Bot whose botname is not set must have a "initiate_botname_not_set" error' );
        }
        public function testInitiateRespondWithoutVersion() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'username' => 'vitsalis'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response does not have a version' );
            $this->assertEquals( 'initiate_version_not_set', $result[ 'errors' ][ 0 ], 'Bot whose version is not set must have a "initiate_version_not_set" error' );
        }
        public function testIniatiateRespondWithoutUsername() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response does not have a username' );
            $this->assertEquals( 'initiate_username_not_set', $result[ 'errors' ][ 0 ], 'Bot whose username is not set must have a "initiate_username_not_set" error' );
        }
        public function testInitiateRespondAdditionalData() {
            $result = $this->initiateWithJsonAndGetErrors( json_encode( [
                'botname' => 'suprabot',
                'version' => '0.1.0',
                'username' => 'vitsalis',
                'additional' => 'shit'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotExcpetion must be caught when response has additional data' );
            $this->assertEquals( 'initiate_additional_data', $result[ 'errors' ][ 0 ], 'Bot whose username is not set must have a "initiate_additional_data" error' );
        }
        protected function gameRequestWithJsonAndGetErrors( $json ) {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );
            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendGameRequest( $game );
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testGameRequest() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendGameRequest' ), 'GraderBot object must export a "sendGameRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( json_encode( [] ) );
            $bot->curlConnectionObject = $curlConnectionMock;
            $bot->sendGameRequest( $game );
            $data = GraderSerializer::gameRequestParams( $game );

            $this->assertEquals( $botbase . '/game', $curlConnectionMock->url, 'GameRequest must send a request to the URL {{botbase}}/game' );
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'GameRequest must be a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'GameRequest must execute curl process' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'gameid' ] ), 'gameid must exist in curl connection' );
            $this->assertEquals( $data[ 'gameid' ], $curlConnectionMock->data[ 'gameid' ], 'gameid must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'W' ] ), 'W must exist in curl connection' );
            $this->assertEquals( $data[ 'W' ], $curlConnectionMock->data[ 'W' ], 'W must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'H' ] ), 'H must exist in curl connection' );
            $this->assertEquals( $data[ 'H' ], $curlConnectionMock->data[ 'H' ], 'H must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'M' ] ), 'M must exist in curl connection' );
            $this->assertEquals( $data[ 'M' ], $curlConnectionMock->data[ 'M' ], 'M must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'MAX_HP' ] ), 'MAX_HP must exist in curl connection' );
            $this->assertEquals( $data[ 'MAX_HP' ], $curlConnectionMock->data[ 'MAX_HP' ], 'MAX_HP must be sent properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'players' ] ), 'players must exist in curl connection' );
            $this->assertEquals( $data[ 'players' ], $curlConnectionMock->data[ 'players' ], 'players must be sent properly to curl' );
        }
        public function testGameRespondValidJson() {
            $result = $this->gameRequestWithJsonAndGetErrors( json_encode( [] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if the response is valid' );
            $this->assertTrue( empty( $result[ 'errors' ] ), 'There should be no errors if the json is valid' );
        }
        public function testGameRespondInvalidJson() {
            $result = $this->gameRequestWithJsonAndGetErrors( 'not_correct_answer' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'game_invalid_json', $result[ 'errors' ][ 0 ], 'A "game_invalid_json" error must be recorded when bot responds with invalid json' );
        }
        public function testGameRespondAdditionalData() {
            $result = $this->gameRequestWithJsonAndGetErrors( json_encode( [
                'additional' => 'shit'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response has additional data' );
            $this->assertEquals( 'game_additional_data', $result[ 'errors' ][ 0 ], 'A "game_additional_data" error must be recorded when bot responds with additional data' );
        }
        protected function roundRequestWithJsonAndGetErrors( $json ) {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();
            $round = $game->rounds[ 0 ];
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );

            $curlConnectionMock = new CurlConnectionMock();
            $curlConnectionMock->makeRespondWith( $json );

            $bot->curlConnectionObject = $curlConnectionMock;

            $caught = false;
            try {
                $bot->sendRoundRequest( $round );
            }
            catch ( GraderBotException $e ) {
                $caught = true;
            }

            return [
                'caught' => $caught,
                'errors' => $bot->errors
            ];
        }
        public function testRoundRequest() {
            $game = $this->buildGame();
            $game->initiateAttributes();
            $game->genesis();
            $round = $game->rounds[ 0 ];
            $user = $game->users[ 0 ];
            $bot = new GraderBot( $user );
            $botbase = $user->boturl;

            $this->assertTrue( method_exists( $bot, 'sendRoundRequest' ), 'GraderBot object must export a "sendRoundRequest" function' );

            $curlConnectionMock = new CurlConnectionMock();
            $bot->curlConnectionObject = $curlConnectionMock;
            $curlConnectionMock->makeRespondWith( json_encode( [
                'creatureid' => 1,
                'desire' => 'ATTACK',
                'direction' => 'NORTH'
            ] ) );
            $bot->sendRoundRequest( $round );
            $data = GraderSerializer::roundRequestParams( $round );

            $this->assertEquals( $botbase . "/game/$game->id/round", $curlConnectionMock->url, 'RoundRequest must send a request to the URL {{botbase}}/game/{{gameid}}/round' ); 
            $this->assertEquals( 'POST', $curlConnectionMock->requestMethod, 'RoundRequest must be a POST request' );
            $this->assertTrue( $curlConnectionMock->executed, 'RoundRequest must execute curl process' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'round' ] ), 'round must exist in curl connection' );
            $this->assertEquals( $data[ 'round' ], $curlConnectionMock->data[ 'round' ], 'round must be send properly to curl' );

            $this->assertTrue( isset( $curlConnectionMock->data[ 'map' ] ), 'map must exist in curl connection' );
            $this->assertEquals( $data[ 'map' ], $curlConnectionMock->data[ 'map' ], 'map must be send properly to curl' );
        }
        public function testRoundRespondValidJson() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'creatureid' => 1,
                'desire' => 'ATTACK',
                'direction' => 'NORTH'
            ] ) );

            $this->assertFalse( $result[ 'caught' ], 'A GraderBotException must not be caught if the response is valid' );
            $this->assertTrue( empty( $result[ 'errors' ] ), 'There should be no errors if the json is valid' );
        }
        public function testRoundRespondInvalidJson() {
            $result = $this->roundRequestWithJsonAndGetErrors( 'invalid_json' );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid json' );
            $this->assertEquals( 'round_invalid_json', $result[ 'errors' ][ 0 ], 'A "round_invalid_json" error must be recorded if the bot responds with invalid json' );
        }
        public function testRoundRespondWithoutCreatureid() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'desire' => 'ATTACK',
                'direction' => 'NORTH'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'round_creatureid_not_set', $result[ 'errors' ][ 0 ], 'A "round_creatureid_not_set" error must be recorded when bot responds with creatureid not set' );
        }
        public function testRoundRespondWithoutDesire() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'creatureid' => 1,
                'direction' => 'NORTH'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'round_desire_not_set', $result[ 'errors' ][ 0 ], 'A "round_desire_not_set" error must be recorded when bot responds with desire not set' );
        }
        public function testRoundRespondWithoutDirection() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'creatureid' => 1,
                'desire' => 'ATTACK'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response is invalid' );
            $this->assertEquals( 'round_direction_not_set', $result[ 'errors' ][ 0 ], 'A "round_direction_not_set" error must be recorded when bot responds without direction set' );
        }
        public function testRoundRespondAdditionalData() {
            $result = $this->roundRequestWithJsonAndGetErrors( json_encode( [
                'creatureid' => 1,
                'desire' => 'ATTACK',
                'direction' => 'NORTH',
                'additional' => 'shit'
            ] ) );

            $this->assertTrue( $result[ 'caught' ], 'A GraderBotException must be caught if the response has additional data' );
            $this->assertEquals( 'round_additional_data', $result[ 'errors' ][ 0 ], 'A "round_additional_data" error must be recorded when the bot responds with additional data' );
        }
    }

    return new GraderBotTest();
?>
