<?php
    class MigrationRunController extends ControllerBase {
        public function create( $env, $name = '' ) {
            global $config;

            require_once 'models/migration.php';

            if ( !empty( $name ) ) {
                $this->run( $name, $env );
            }
            else {
                try {
                    $migrations = Migration::findUnexecuted( $env );
                }
                catch ( ModelNotFoundException $e ) {
                    $migrations = Migration::findAll();
                } 
                foreach ( $migrations as $name ) {
                    $this->run( $name, $env ); 
                }
            }
            require_once 'views/migration/results.php';
        }
        public function createView() {
            require_once 'models/migration.php';
            
            try {
                $last = Migration::findLast();
            }
            catch ( ModelNotFoundException $e ) {
            }
            $migrations = Migration::findAll();
            require_once 'views/migration/create.php';
        }
        protected function run( $name, $env ) {
            $this->environment = $env;
            $this->init();

            try {
                require_once 'database/migration/' . $name;
            }
            catch ( MigrationException $e ) {
                throw $e;
            }
            Migration::createLog( $name, $env );
        }
    }
?>
