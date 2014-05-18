<?php
    class DbconfigController extends ControllerBase {
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
                require_once 'views/database/notwritable.php';
            }
        }
        public function createView( $error, $dbSaid ) {
            global $config;
            $oldConfig = [ 'user'   => '',
                           'pass'   => '',
                           'dbname' => '' ];
            $oldConfig = [ 'db' => $oldConfig ];
            $oldConfig = array_replace_recursive( $oldConfig, $config );

            require_once 'views/database/create.php';
        }

    }
?>
