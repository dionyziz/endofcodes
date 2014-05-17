<?php
    class DbconfigController extends ControllerBase {
        protected function dbInit() {}

        public function create( $user, $pass, $dbname ) {
            require_once 'models/database.php';

            $entries = [ 'user'   => $user,
                         'pass'   => $pass,
                         'dbname' => $dbname ];
            $environment = 'development';
            try {
                createConfig( $entries, $environment );
                go();
            }
            catch ( DBExceptionNotWritable $e ) {
                $content = $e->content;
                require_once 'views/database/notwritable.php';
            }
        }
        public function createView( $error, $dbSaid ) {
            require_once 'models/database.php';

            $environment = 'development';
            $configLocal = loadConfig( $environment );

            require_once 'views/database/create.php';
        }

    }
?>
