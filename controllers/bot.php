<?php
    class BotController extends ControllerBase {
        public function update( $boturl = '' ) {
            if ( empty( $boturl ) ) {
                go( 'bot', 'update', [ 'boturl_empty' => true ] );
            }
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            $user->boturl = $boturl; 
            $user->save();
        }
        public function updateView( $boturl_empty, $boturl_invalid, $bot_success, $bot_not_success, $invalid_hostname, $invalid_ip, 
                $port_forward_incorrect, $initiation_incorrect, $json_incorrect, $invalid_json_dictionary, $username_incorrect     ) {
            require_once 'views/bots/update.php'; 
        } 
    }
?>
