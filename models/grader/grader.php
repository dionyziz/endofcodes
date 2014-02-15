<?php
    require_once 'models/curl.php';
    require_once 'models/grader/serializer.php';
    require_once 'models/grader/bot.php';
    require_once 'models/error.php';

    class Grader {
        public $registeredBots;
        public $registeredUsers;
        protected $game;
        protected $users;
        public $bots = [];

        public function __construct( $users, $game, $graderBotObject = 'GraderBot' ) {
            assert( $game instanceof Game, '$grader->game is not an instance of Game' );
            $this->users = $users;
            foreach ( $users as $user ) {
                assert( $user instanceof User, '$grader->users is not a collection of users' );
                $this->bots[] = new $graderBotObject( $user );
            }
            $this->game = $game;
        }
        public function initiate() {
            $this->registeredBots = [];
            $this->registeredUsers = [];

            foreach ( $this->bots as $bot ) {
                try {
                    $bot->sendInitiateRequest();
                    $this->registeredBots[] = $bot;
                    $this->registeredUsers[] = $bot->user;
                }
                catch ( GraderBotException $e ) {
                    $error = new Error();
                    $error->game = $this->game;
                    $error->user = $bot->user;
                    $error->error = $e->error;
                    $error->save();
                }
            }
        }
        public function createGame() {
            $this->game->users = $this->registeredUsers;
            $this->game->initiateAttributes();
            $this->game->save();
            $this->game->genesis();

            foreach ( $this->registeredBots as $bot ) {
                try {
                    $bot->sendGameRequest( $this->game );
                }
                catch ( GraderBotException $e ) {
                    $error = new Error();
                    $error->game = $this->game;
                    $error->user = $bot->user;
                    $error->error = $e->error;
                    $error->save();
                }
            }
        }
        public function nextRound() {
            $round = $this->game->getCurrentRound();

            foreach ( $this->registeredBots as $bot ) {
                try {
                    $bot->sendRoundRequest( $round );
                }
                catch ( GraderBotException $e ) {
                    $error = new Error();
                    $error->game = $this->game;
                    $error->user = $bot->user;
                    $error->error = $e->error;
                    $error->save();
                }
            }
            /*
            // resolution?!
            ...->nextRound();

            foreach ( bot ... ) {
                foreach ( ...->errors as $error ) {
                    if ( $error[ 'roundid' ] == $currentround ) {
                        // this is a new error
                        $error = new Error( $this->game->id, $bot->user->id, $error[ 'description' ] );
                        $error->save();
                    }
                }
            }
            */
        }
    }
?>
