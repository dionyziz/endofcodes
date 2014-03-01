<?php
    class BotController extends ControllerBase {
        public function update( $boturl = '' ) {
            require_once 'models/grader/bot.php';
            if ( empty( $boturl ) ) {
                go( 'bot', 'update', [ 'boturl_empty' => true ] );
            }
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            $user->boturl = $boturl; 
            $bot = new GraderBot( $user );
            try {
                $bot->sendInitiateRequest(); 
            }
            catch ( GraderBotException $e ) {
                //switch ( $e ) {
                //    case 'initiate_could_not_resolve': 
                //        $error = $invalid_hostname;
                //        break; 
                //    case 'initiate_could_not_connect':
                //        $error = $bot_unreachable;
                //        break;
                //    case 'initiate_invalid_json': 
                //        $error = $invalid_json;
                //        break; 
                //    case 'initiate_username_mismatch':
                //        $error = $username_mismatch;
                //        break;
                //} 
                $error = str_replace( "initiate_","",$url );
                go( 'bot', 'update', [ $error => true ] );
            }
            $user->save();
            go( 'bot', 'update', [ 'bot_success' => true ] );
        }
        public function updateView( $boturl_empty, $boturl_invalid, $bot_success, $bot_not_success, $could_not_resolve, 
                $could_not_connect, $username_mismatch, $port_forward_incorrect, $initiation_incorrect, $invalid_json, 
                $invalid_json_dictionary ) {
            require_once 'views/bots/update.php'; 
        } 
    }
?>
