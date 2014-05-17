<?php
    abstract class AuthenticatedController extends ControllerBase {
        protected function requireLogin() {
            if ( !isset( $_SESSION[ 'user' ] ) ) {
                throw new HTTPUnauthorizedException( 'You must be logged in to access this resource.' );
            }
        }
        protected function requireDeveloper() {
            $this->requireLogin();
            if ( !$_SESSION[ 'user' ]->isDeveloper() ) {
                throw new HTTPUnauthorizedException( 'You must be a developer to access this resource.' );
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
