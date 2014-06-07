<?php
    class DebuggingController extends AuthenticatedController {
        public function update( $enable = true ) {
            $this->requireDeveloper();

            $_SESSION[ 'debug' ] = ( bool )$enable;
        }
    }
?>
