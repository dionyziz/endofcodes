<?php
    class DbconfigController extends ControllerBase {
        public function create($user, $pass, $dbname) {
            require_once 'models/database.php';

            $entries = [ 'user'   => $user, 
            			 'pass'   => $pass,
            			 'dbname' => $dbname ];
            $environment = 'development';
            $content = createConfig( $entries, $environment);
            if ( empty($content) ) {
            	throw new RedirectException( '' );
            }
            require_once 'views/database/results.php';
        }
        public function createView( $error, $DbSaid ) {
        	require_once 'views/database/create.php';
        }

    }
?>