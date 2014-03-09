<?php
    class BotController extends AuthenticatedController {
        public function update( $boturl = '' ) {
            $this->requireLogin();

            require_once 'models/grader/bot.php';

            if ( empty( $boturl ) ) {
                go( 'bot', 'update', [ 'boturl_empty' => true ] );
            }
            if ( !filter_var( $boturl, FILTER_VALIDATE_URL ) ) {
                go( 'bot', 'update', [ 'boturl_invalid' => true ] );
            }
            $user = $_SESSION[ 'user' ];
            $user->boturl = $boturl; 
            $bot = new GraderBot( $user );
            try {
                $bot->sendInitiateRequest(); 
            }
            catch ( GraderBotException $e ) {
                $error = end( $bot->errors );
                $expected = $error[ 'expected' ];
                $actual = $error[ 'actual' ];
                $error = $error[ 'error' ];
                $error = str_replace( "initiate_", "", $error );
                if ( strpos( $error, '_not_set' ) ) {
                    $error = 'invalid_json_dictionary';
                }
                go( 'bot', 'update', [ 'bot_fail' => true, 'error' => $error, 'actual' => $actual, 'expected' => $expected ] );
            }
            $user->save();
            go( 'bot', 'update', [ 'bot_success' => true ] );
        }
        public function updateView( $boturl_empty, $boturl_invalid, $bot_success, $bot_fail, $error, $expected, $actual ) {
            $this->requireLogin();

            require_once 'models/grader/bot.php';
            require_once 'views/bot/update.php';
        } 
    }
?>
