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
            if ( $game->ended ) {
                go();
            }

            go( 'game', 'update', [ 'gameid' => $game->id ] );
        }
        public function createView() {
            require 'views/game/create.php';
        }
        public function update( $gameid, $finishit = false ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }

            if ( $game->ended ) {
                go();
            }

            $grader = new Grader( $game );
            do {
                if ( $game->ended ) {
                    go();
                }

                $grader->nextRound();
            } while ( $finishit );

            go( 'game', 'update', compact( 'gameid' ) );
        }
        public function view( $gameid, $roundid = false ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            if ( $roundid !== false ) {
                if ( !isset( $game->rounds[ $roundid ] ) ) {
                    throw new HTTPNotFoundException();
                }
                $round = $game->rounds[ $roundid ];
            }
            else {
                $round = $game->getCurrentRound();
            }
            require 'views/game/view.php';
        }
        public function updateView( $gameid ) {
            try {
                $game = new Game( $gameid );
            }
            catch ( ModelNotFoundException $e ) {
                throw new HTTPNotFoundException();
            }
            require 'views/game/update.php';
        }
    }
?>
