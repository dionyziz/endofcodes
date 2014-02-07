<?php
    include_once 'models/curl.php';
    include_once 'models/grader/serializer.php';
    include_once 'models/grader/bot.php';

    class Grader {
        protected $bots;
        protected $game;

        public function initiate() {
            $bot->sendInitiateRequest();
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
