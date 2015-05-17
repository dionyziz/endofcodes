<?php
    class DbconfigController extends ControllerBase {
        protected $method = 'create';
        // protected function dbInit() {} // deprecated dunno why this was here. 

        public function create( $user, $pass, $dbname ) {
            $entries = compact( 'user', 'pass', 'dbname' );
            $entries = [ 'db' => $entries ];
            try {
                updateConfig( $entries, $this->environment );
                $this->runMigrations();
                go();
            }
            catch ( FileNotWritableException $e ) {
                $content = $e->content;
                require 'views/dbconfig/notwritable.php';
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

            require 'views/dbconfig/create.php';
        }
        private function runMigrations(){
            require_once 'models/migration.php';
            $migrations = Migration::findAll();
            $this->init();
            foreach ( $migrations as $migration ){
                try {
                    require_once 'database/migration/' . $migration;
                }
                catch( MigrationException $ex ){
                    // Maybe change this behaviour to revert changes till now or something...
                    throw $ex;
                }
            }
        }

    }
?>
