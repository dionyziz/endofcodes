<?php
    class BotController extends AuthenticatedController {
        public function update( $boturl = '' ) {
            $this->requireLogin();

            require_once 'models/grader/bot.php';
            require_once 'models/error.php';

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
                $error = new Error();
                $error->user = $bot->user;
                $errorInfo = end( $bot->errors );
                $error->expected = $errorInfo[ 'expected' ];
                $error->actual = $errorInfo[ 'actual' ];
                $error->description = str_replace( "initiate_", "", $errorInfo[ 'error' ] );
                if ( strpos( $error->description, '_not_set' ) ) {
                    $error->description = 'invalid_json_dictionary';
                }
                $error->save();
                go( 'bot', 'update', [ 'bot_fail' => true, 'error' => $error->description, 'actual' => $error->actual, 'expected' => $error->expected ] );
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
