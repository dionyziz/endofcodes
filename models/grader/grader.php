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

        public function __construct( $users, $game, $graderBotClass = 'GraderBot' ) {
            assert( $game instanceof Game, '$grader->game is not an instance of Game' );
            $this->game = $game;
            $this->users = $users;
            foreach ( $users as $user ) {
                assert( $user instanceof User, '$grader->users is not a collection of users' );
                $bot = new $graderBotClass( $user );
                $bot->game = $game;
                $this->bots[] = new $graderBotClass( $user );
            }
        }
        public function initiate() {
            $this->registeredBots = [];
            $this->registeredUsers = [];

            foreach ( $this->bots as $bot ) {
                try {
                    $bot->sendInitiateRequest();
                    $this->registeredBots[] = $bot;
                    $bot->game = $this->game;
                    $this->registeredUsers[] = $bot->user;
                }
                catch ( GraderBotException $e ) {
                    $error = new Error();
                    $error->user = $bot->user;
                    $error->game = $this->game;
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
                    $error->user = $bot->user;
                    $error->game = $this->game;
                    $error->error = $e->error;
                    $error->save();
                }
            }
        }
        public function nextRound() {
            $round = $this->game->getCurrentRound();

            foreach ( $this->registeredBots as $bot ) {
                try {
                    $creatureCollection = $bot->sendRoundRequest( $round );
                    foreach ( $creatureCollection as $creatureIntent ) {
                        $round->creatures[ $creatureIntent->id ]->intent = $creatureIntent->intent;
                    }
                }
                catch ( GraderBotException $e ) {
                    $error = new Error();
                    $error->user = $bot->user;
                    $error->game = $this->game;
                    $error->error = $e->error;
                    $error->save();
                }
            }

            $this->game->nextRound();

            foreach ( $this->game->getCurrentRound()->errors as $userid => $errors ) {
                foreach ( $errors as $description ) {
                    $error = new Error();
                    $error->game = $this->game;
                    $error->user = new User( $userid );
                    $error->error = $description;
                    $error->save();
                }
            }
        }
    }
?>
