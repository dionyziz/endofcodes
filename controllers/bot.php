<?php
    class BotController extends ControllerBase {
        public function update( $boturl = '' ) {
            require_once 'models/curl.php';
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
                $error = end( $bot->errors );
                $error = str_replace( "initiate_", "", $error );
                if ( strpos( $error, '_not_set' ) ) {
                    $error = 'invalid_json_dictionary';
                }
                go( 'bot', 'update', [ 'bot_not_success' => true, 'error' => $error ] );
            }
            $user->save();
            go( 'bot', 'update', [ 'bot_success' => true ] );
        }
        public function updateView( $boturl_empty, $boturl_invalid, $bot_success, $bot_not_success, $error ) {
            require_once 'views/bots/update.php'; 
        } 
    }
?>
