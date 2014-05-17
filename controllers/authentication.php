<?php
    abstract class AuthenticatedController extends ControllerBase {
        protected function requireLogin() {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException();
            }
        }
        protected function requireDeveloper() {
            $this->requireLogin();
            if ( !$_SESSION[ 'user' ]->isDeveloper() ) {
                throw new HTTPUnauthorizedException();
            }
        }
    }
?>
