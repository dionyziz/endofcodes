<?php
    class DbconfigController extends ControllerBase {
        protected $method = 'create';
        protected function dbInit() {}

        public function create( $user, $pass, $dbname ) {
            if ( $this->environment == 'production' ) {
                go(); // disable in production
            }
            
            $entries = compact( 'user', 'pass', 'dbname' );
            $entries = [ 'db' => $entries ];
            try {
                updateConfig( $entries, $this->environment );
                go();
            }
            catch ( FileNotWritableException $e ) {
                $content = $e->content;
                require 'views/dbconfig/notwritable.php';
            }
        }
        public function createView( $error, $dbSaid ) {
            global $config;
            
            if ( $this->environment == 'production' ) {
                go(); // disable in production
            }
            
            $oldConfig = [
                'db' => [
                    'user'   => '',
                    'pass'   => '',
                    'dbname' => ''
                ]
            ];
            $oldConfig = array_replace_recursive( $oldConfig, $config );

            require 'views/dbconfig/create.php';
        }

    }
?>
