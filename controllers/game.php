<?php
    require_once 'models/grader/grader.php';

    class GameController extends ControllerBase {
        public function create() {
            $game = new Game();
            $game->save();
            $users = User::findAll();
            $grader = new Grader( $game, $users );
            $grader->initiateBots();
            $grader->initiate();
            $grader->createGame();

            go( 'game', 'update', [ 'gameid' => $game->id ] );
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $gameid ) {
            $game = new Game( $gameid );

            $grader = new Grader( $game );
            try {
                $grader->nextRound();
            }
            catch ( WinnerException $e ) {
                go( 'game', 'view', [ 'id' => $e->winnerid ] );
            }

            go( 'game', 'update', compact( 'gameid' ) );
        }
        public function view( $id ) {
            $user = new User( $id );
            require 'views/game/view.php';
        }
        public function updateView( $gameid ) {
            require 'views/game/update.php';
        }
    }
?>
