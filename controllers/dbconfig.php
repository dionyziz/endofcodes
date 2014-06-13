<?php
    class DbconfigController extends ControllerBase {
        protected $method = 'create';
        protected function dbInit() {}

        public function create( $user, $pass, $dbname ) {
            $entries = compact( 'user', 'pass', 'dbname' );
            $entries = [ 'db' => $entries ];
            try {
                updateConfig( $entries, $this->environment );
                go();
            }
            catch ( FileNotWritableException $e ) {
                $content = $e->content;
                require 'views/database/notwritable.php';
            }
        }
        public function createView( $error, $dbSaid ) {
            global $config;
            $oldConfig = [
                'db' => [
                    'user'   => '',
                    'pass'   => '',
                    'dbname' => ''
                ]
            ];
            $oldConfig = array_replace_recursive( $oldConfig, $config );

            require 'views/database/create.php';
        }

    }
?>
