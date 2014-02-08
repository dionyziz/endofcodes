<?php
    include_once 'models/curl.php';
    include_once 'models/grader/serializer.php';
    include_once 'models/grader/bot.php';

    class Grader {
        public $bots;
        public $registeredBots;
        protected $game;

        public function initiate() {
            $this->registeredBots = [];
            foreach ( $this->bots as $bot ) {
                try {
                    $bot->sendInitiateRequest();
                    $this->registeredBots[] = $bot;
                }
                catch ( GraderBotException $e ) {
                }
            }
        }
        public function createGame() {
            foreach ( $this->bots as $bot ) {
                $bot->sendGameRequest( $this->game );
            }
        }
        public function nextRound() {
            $round = $this->game->getCurrentRound();
            foreach ( $this->bots as $bot ) {
                $bot->sendRoundRequest( $round );
            }
        }
    }
?>
