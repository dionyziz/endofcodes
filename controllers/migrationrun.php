<?php
    class MigrationRunController extends ControllerBase {
        public function create( $name = '', $env ) {
            $GLOBALS[ 'env' ] = $env;

            require_once 'models/migration/base.php';

            if ( !empty( $name ) ) {
                $this->run( $name, $env );
            }
            else {
                $migrations = Migration::getUnexecuted( $env );
                foreach( $migrations as $name ) {
                    $this->run( $name, $env ); 
                }
            }
        }
        public function createView() {
            require_once 'models/migration/base.php';
            
            $last = Migration::getLast();
            $migrations = Migration::findAll();
            require_once 'views/migration/create.php';
        }

        protected function run( $name, $env ) {
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
