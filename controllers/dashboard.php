<?php
    class DashboardController extends ControllerBase {
        public function view() {
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            require_once 'models/game.php';
            try {
                $game = Game::getLastGame();
                $ratings = $game->getGlobalRatings();
            }
            catch ( ModelNotFoundException $e ) {
            }
            require_once 'views/home.php';
        }
    }
?>
