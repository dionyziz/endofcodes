<?php
    class BotController extends ControllerBase {
        public function update( $boturl ) {
            echo 'AWESOME BITCH. IT IS WORKING!!';  
        }
        public function updateView( $boturl_empty, $boturl_invalid ) {
            require_once 'views/bots/update.php'; 
        } 
    }
?>
