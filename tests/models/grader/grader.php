<?php
    require_once 'models/grader/grader.php';
    require_once 'tests/models/grader/bot.php';

    class GraderBotMock implements GraderBotInterface {
        public $boturlValid;
        public $gameResponseValid;
        public $roundResponseValid;
        public $user;

        public function __construct( User $user ) {
            $this->user = $user;
        }
        public function sendInitiateRequest() {
            if ( !$this->boturlValid ) {
                throw new GraderBotException( 'initiate_error' );
            }
        }
        public function sendGameRequest( Game $game ) {
            if ( !$this->gameResponseValid ) {
                throw new GraderBotException( 'game_error' );
            }
        }
        public function sendRoundRequest( Round $round ) {
            if ( !$this->roundResponseValid ) {
                throw new GraderBotException( 'round_error' );
            }
            return [];
        }
    }

    class GraderTest extends UnitTestWithFixtures {
        protected $users;
        protected $game;

        public function setUp() {
            $user1 = $this->buildUser( 'dionyziz' );
            $user2 = $this->buildUser( 'pkakelas' );

            $this->users = [
                $user1->id => $user1,
                $user2->id => $user2
            ];

            $this->game = new Game();
            $this->game->save();
        }
        public function testLoadGraderFromExistingGame() {
            $round = new Round();
            $round->id = 0;
            $round->game = $this->game;
            $user = $this->users[ 1 ];
            $this->game->users = [ $user->id => $user ];
            $round->creatures = [ 1 => $this->buildCreature( 1, 0, 0, $user ) ];
            $round->save();
            $this->game->rounds[ 0 ] = $round;

            $grader = new Grader( $this->game );

            $this->assertTrue( isset( $grader->registeredUsers ), 'Constructor of grader must set the users' );
            $this->assertEquals( 1, count( $grader->registeredUsers ), 'Constructor of grader must find the correct number of users' );
            $this->assertEquals( $user->id, $grader->registeredUsers[ 1 ]->id, 'Constructor of grader must find the correct users' );
        }
        public function testIncludeUsersWithInvalidBots() {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = false;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->bots ), 'The number of bots must be the same as the number of users' );
            $this->assertEquals( 0, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );

            $error1 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 1 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 2 ]->id );

            $this->assertTrue( !empty( $error1 ), 'A user with invalid boturl must have errors' );
            $this->assertTrue( !empty( $error2 ), 'A user with invalid boturl must have errors' );

            $this->assertEquals( 1, count( $error1 ), 'A user must get one error if they have an invalid boturl' );
            $this->assertEquals( 1, count( $error2 ), 'A user must get one error if they have an invalid boturl' );

            $this->assertEquals( 'initiate_error', $error1[ 0 ]->description, 'A user must get a "initiate_error" error if he has an invalid boturl' );
            $this->assertEquals( 'initiate_error', $error2[ 0 ]->description, 'A user must get a "initiate_error" error if he has an invalid boturl' );
        }
        public function testIncludeUsersWithValidBots() {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );

            $error1 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 1 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 2 ]->id );

            $this->assertTrue( empty( $error1 ), 'A user with valid boturl must not have errors' );
            $this->assertTrue( empty( $error2 ), 'A user with valid boturl must not have errors' );
        }
        public function testCreateGameInvalidResponse() {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = false;
            }
            $grader->initiate();
            $grader->createGame();

            $error1 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 1 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 2 ]->id );

            $this->assertTrue( !empty( $error1 ), 'A user with invalid game response must have errors' );
            $this->assertTrue( !empty( $error2 ), 'A user with invalid game response must have errors' );

            $this->assertEquals( 1, count( $error1 ), 'A user must get one error if they have an invalid game response' );
            $this->assertEquals( 1, count( $error2 ), 'A user must get one error if they have an invalid game response' );

            $this->assertEquals( 'game_error', $error1[ 0 ]->description, 'A user must get a "game_error" error if he has an invalid game response' );
            $this->assertEquals( 'game_error', $error2[ 0 ]->description, 'A user must get a "game_error" error if he has an invalid game response' );
        }
        public function testCreateGameValidResponse() {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = true;
            }
            $grader->initiate();
            $grader->createGame();

            $error1 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 1 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 2 ]->id );

            $this->assertTrue( empty( $error1 ), 'A user with valid game response must not have errors' );
            $this->assertTrue( empty( $error2 ), 'A user with valid game response must not have errors' );
        }
        protected function nextRoundAndGetErrors( $responseValid ) {
            $grader = new Grader( $this->game, $this->users );
            $grader->graderBotClass = 'GraderBotMock';
            $grader->initiateBots();
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
                $bot->gameResponseValid = true;
                $bot->roundResponseValid = $responseValid;
            }
            $grader->initiate();
            $grader->createGame();
            $grader->nextRound();

            $error1 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 1 ]->id );
            $error2 = Error::findErrorsByGameAndUser( $this->game->id, $this->users[ 2 ]->id );

            return compact( 'error1', 'error2' );
        }
        public function testNextRoundInvalidResponse() {
            $errors = $this->nextRoundAndGetErrors( false );

            $this->assertTrue( !empty( $errors[ 'error1' ] ), 'A user with invalid round response must have errors' );
            $this->assertTrue( !empty( $errors[ 'error2' ] ), 'A user with invalid round response must have errors' );

            $this->assertEquals( 1, count( $errors[ 'error1' ] ), 'A user must get one error if they have an invalid round response' );
            $this->assertEquals( 1, count( $errors[ 'error2' ] ), 'A user must get one error if they have an invalid round response' );

            $this->assertEquals( 'round_error', $errors[ 'error1' ][ 0 ]->description, 'A user must get a "round_error" error if he has an invalid round response' );
            $this->assertEquals( 'round_error', $errors[ 'error2' ][ 0 ]->description, 'A user must get a "round_error" error if he has an invalid round response' );
        }
        public function testNextRoundValidResponse() {
            $errors = $this->nextRoundAndGetErrors( true );

            $this->assertTrue( empty( $errors[ 'error1' ] ), 'A user with valid round response must not have errors' );
            $this->assertTrue( empty( $errors[ 'error2' ] ), 'A user with valid round response must not have errors' );
        }
        public function testRoundResolution() {
            $game = new Game();
            $game->users = $this->users;
            $game->initiateAttributes();
            $game->id = 1;
            $game->exists = true;
            $creature1 = $this->buildCreature( 1, 1, 1, $this->users[ 1 ], $game );
            $creature2 = $this->buildCreature( 2, 2, 2, $this->users[ 2 ], $game );
            $round = new Round();
            $round->creatures = [ 1 => $creature1, 2 => $creature2 ];
            $round->game = $game;
            $game->rounds[ 0 ] = $round;
            $game->users = $this->users;
            $grader = new Grader( $game );
            $grader->registeredUsers = $this->users;
            $bot1 = new GraderBot( $this->users[ 1 ] );
            $bot1->game = $game;
            $bot1->curlConnectionObject = new CurlConnectionMock();
            $bot1->curlConnectionObject->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 1,
                        'action' => 'MOVE',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );
            $bot2 = new GraderBot( $this->users[ 2 ] );
            $bot2->game = $game;
            $bot2->curlConnectionObject = new CurlConnectionMock();
            $bot2->curlConnectionObject->makeRespondWith( json_encode( [
                'intent' => [
                    [
                        'creatureid' => 2,
                        'action' => 'MOVE',
                        'direction' => 'NORTH'
                    ]
                ]
            ] ) );

            $grader->registeredBots = [ $bot1, $bot2 ];
            $grader->nextRound();

            $this->assertTrue( isset( $game->rounds[ 1 ] ), 'A new round must be created after nextRound is called' );
            $this->assertTrue( is_object( $game->rounds[ 1 ] ), 'The new round must be an object' );

            $this->assertTrue( isset( $game->rounds[ 1 ]->creatures ), 'the new round must have creatures' );
            $this->assertTrue( is_array( $game->rounds[ 1 ]->creatures ), 'the new round must have a creatures array' );

            $newCreature1 = $game->rounds[ 1 ]->creatures[ 1 ];
            $newCreature2 = $game->rounds[ 1 ]->creatures[ 2 ];

            $this->assertEquals( $creature1->id, $newCreature1->id, 'The creature must not change id' );
            $this->assertEquals( $creature2->id, $newCreature2->id, 'The creature must not change id' );

            $this->assertEquals( 1, $newCreature1->locationx, 'A creature must move in x axis if it is specified' );
            $this->assertEquals( 2, $newCreature1->locationy, 'A creature must move in y axis if it is specified' );

            $this->assertEquals( 2, $newCreature2->locationx, 'A creature must move in x axis if it is specified' );
            $this->assertEquals( 3, $newCreature2->locationy, 'A creature must move in y axis if it is specified' );
        }
        protected function buildGameWithUserAndCreature() {
            $game = new Game();
            $game->save();
            $game->users = [ 1 => $this->buildUser( 'vitsalis' ) ];
            $game->rounds[ 0 ] = new Round();
            $game->rounds[ 0 ]->creatures = [ 1 => $this->buildCreature( 1, 1, 1, $game->users[ 1 ] ) ];

            return $game;
        }
        public function testFindBotsFromGame() {
            $game = $this->buildGameWithUserAndCreature();
            $grader = new Grader( $game );

            $this->assertTrue( isset( $grader->registeredUsers ), "Grader must get its users from the game" );
            $this->assertTrue( isset( $grader->registeredBots ), "Grader must get its bots from the game" );

            $this->assertEquals( $game->users[ 1 ]->id, $grader->registeredUsers[ 1 ]->id, 'Grader must get valid users from the game' );
            $this->assertEquals( $game->users[ 1 ]->id, $grader->registeredBots[ 0 ]->user->id, 'Grader must get valid bots from the game' );
        }
        public function testFindWinner() {
            $game = $this->buildGameWithUserAndCreature();
            $grader = new Grader( $game );

            $caught = false;
            try {
                $grader->nextRound();
            }
            catch ( WinnerException $e ) {
                $caught = true;
                $winnerid = $e->winnerid;
            }

            $this->assertTrue( $caught, 'A WinnerException must be caught if there is only one user with alive creatures on a round' );
            $this->assertEquals( $grader->registeredUsers[ 1 ]->id, $winnerid, 'The winner must be the one whose creatures are still alive' );
        }
        public function testBotsIntentsClearedBeforeRound() {
            $game = $this->buildGameWithUserAndCreature();
            $game->rounds[ 0 ]->creatures[ 1 ]->intent = new Intent( ACTION_MOVE, DIRECTION_NORTH );
            $grader = new Grader( $game );
            try {
                $grader->nextRound();
            }
            catch ( WinnerException $e ) {
            }
            foreach ( $game->rounds[ 0 ]->creatures as $creature ) {
                $this->assertEquals( ACTION_NONE, $creature->intent->action, 'Action must be set to ACTION_NONE before the next round starts' );
                $this->assertEquals( DIRECTION_NONE, $creature->intent->direction, 'Direction must be set to direction_NONE before the next round starts' );
            }
        }
    }
    return new GraderTest();
?>
