<?php
    include_once 'models/grader/grader.php';

    class GameController extends ControllerBase {
        public function create() {
            $game = new Game();
            $users = User::findAll();
            $grader = new Grader( $users, $game );
            $grader->initiate();
            $grader->createGame();
        }
        public function createView() {
            include 'views/game/create.php';
        }
        public function update( $grader ) {
            $grader->nextRound();
            $grader->game->nextRound();
        }
    }
?>
