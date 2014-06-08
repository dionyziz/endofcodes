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
            try {
                $user->setBoturl( $boturl );
            }
            catch ( ModelValidationException $e ) {
                go( 'bot', 'update', [ 'bot_fail' => true, 'errorid' => $e->error ] );
            }
            go( 'bot', 'update' );
        }
        public function updateView( $boturl_empty, $boturl_invalid, $bot_fail, $errorid = false ) {
            require_once 'models/error.php';

            $this->requireLogin();
            $user = $_SESSION[ 'user' ];
            if ( $errorid !== false ) {
                $error = new Error( $errorid );
                if ( $error->user->id !== $user->id ) {
                    throw new HTTPUnauthorizedException();
                }
            }

            require_once 'models/grader/bot.php';
            require_once 'views/bot/update.php';
        } 
    }
?>
