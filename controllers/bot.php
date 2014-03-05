<?php
    class BotController extends ControllerBase {
        public function update( $boturl = '' ) {
            require_once 'models/curl.php';
            require_once 'models/grader/bot.php';
            if ( empty( $boturl ) ) {
                go( 'bot', 'update', [ 'boturl_empty' => true ] );
            }
            if ( !filter_var( $boturl, FILTER_VALIDATE_URL ) ) {
                go( 'bot', 'update', [ 'boturl_invalid' => true ] );
            }
            if ( isset( $_SESSION[ 'user' ] ) ) {
                $user = $_SESSION[ 'user' ];
            }
            else {
                throw new HTTPUnauthorizedException();
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
                go( 'bot', 'update', [ 'bot_fail' => true, 'error' => $error ] );
            }
            $user->save();
            go( 'bot', 'update', [ 'bot_success' => true ] );
        }
        public function updateView( $boturl_empty, $boturl_invalid, $bot_success, $bot_fail, $error ) {
            require_once 'views/bots/update.php'; 
        } 
    }
?>
