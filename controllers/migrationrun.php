<?php
    class MigrationRunController extends ControllerBase {
        public function create( $name = '', $env ) {
            global $config;


            require_once 'models/migration.php';

            if ( !empty( $name ) ) {
                $this->run( $name, $env );
            }
            else {
                try {
                    $migrations = Migration::getUnexecuted( $env );
                }
                catch( ModelNotFoundException $e ) {
                    $migrations = Migration::findAll();
                } 
                foreach( $migrations as $name ) {
                    $this->run( $name, $env ); 
                }
            }
        }
        public function createView() {
            require_once 'models/migration.php';
            
            try {
                $last = Migration::findLast();
            }
            catch ( ModelNotFoundException $e ) {
                $last = 'You have not created any logs yet.';
            }
            $migrations = Migration::findAll();
            require_once 'views/migration/create.php';
        }
        protected function run( $name, $env ) {
            $this->init( $env );

            try {
                require_once 'database/migration/' . $name;
            }
            catch ( MigrationException $e ) {
                throw $e;
            }
            Migration::createLog( $name, $env );
            echo "The migration script completed successfully without errors.\n"; 
        }
    }
?>
