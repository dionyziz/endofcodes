<?php
    class ProfilingController extends AuthenticatedController {
        public function update( $enable = true ) {
            $this->requireDeveloper();

            $_SESSION[ 'profiling' ] = ( bool )$enable;
        }
    }
?>
