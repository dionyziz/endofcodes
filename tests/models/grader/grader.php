<?php
    include_once 'models/grader/grader.php';

    class GraderBotMock implements GraderBotInterface {
        public $boturlValid;
        public $user;

        public function __construct( $user ) {
            $this->user = $user;
        }
        public function sendInitiateRequest() {
            if ( !$this->boturlValid ) {
                throw new GraderBotException( 'error' );
            }
        }
        public function sendGameRequest( $game ) {}
        public function sendRoundRequest( $round ) {}
    }

    class GraderTest extends UnitTestWithFixtures {
        public function testIncludeUsersWithInvalidBots() {
            $game = new Game();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $users[ 0 ]->boturl = 'koko';
            $users[ 1 ]->boturl = 'lala';
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = false;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->bots ), 'The number of bots must be the same as the number of users' );
            $this->assertEquals( 0, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );
        }
        public function testIncludeUsersWithValidBots() {
            $game = new Game();
            $users = [ $this->buildUser( 'dionyziz' ), $this->buildUser( 'pkakelas' ) ];
            $users[ 0 ]->boturl = 'localhost/endofcodes/bots/php';
            $users[ 1 ]->boturl = 'localhost/endofcodes/bots/php';
            $grader = new Grader( $users, $game, 'GraderBotMock' );
            foreach ( $grader->bots as $bot ) {
                $bot->boturlValid = true;
            }
            $grader->initiate();

            $this->assertEquals( 2, count( $grader->registeredBots ), 'The number of registered bots must be the same as the number of valid bots' );
            $this->assertEquals( count( $grader->registeredBots ), count( $grader->registeredUsers ), 'The number of registered users must be the same with the number of registered bots' );
        }
    }
    return new GraderTest();
?>
