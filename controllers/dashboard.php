<?php
    class DashboardController extends ControllerBase {
        public function view() {
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            require_once 'models/game.php';
            require_once 'views/home.php';
            $game = Game::getLastGame(); 
            $ratings = $game->getGlobalRatings();
            $ratings = array_slice( $ratings, 0, 10 );
        }
    }
?>
