<?php
    abstract class AuthenticatedController extends ControllerBase {
        protected function requireLogin() {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
        }
    }
?>
