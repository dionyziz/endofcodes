<?php
    class MigrationController extends ControllerBase {
        public function create( $name = '', $env ) {
            $GLOBALS[ 'env' ] = $env;

            require_once 'models/migration/base.php';

            if ( !empty( $name ) ) {
                $this->run( $name );
            }
            else {
                $migrations = Migration::getUnexecuted();
                foreach( $migrations as $name ) {
                    $this->run( $name ); 
                }
            }
        }
        public function createView() {
            require_once 'models/migration/base.php';
            
            $last = Migration::getLast();
            $migrations = Migration::findAll();
            require_once 'views/migration/create.php';
        }

        protected function run( $name ) {
            try {
                require_once 'database/migration/' . $name;
            }
            catch ( MigrationException $e ) {
                throw $e;
            }
            Migration::createLog( $name );
            echo "The migration script completed successfully without errors.\n"; 
        }
    }
