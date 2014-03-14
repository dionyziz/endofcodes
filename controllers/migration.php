<?php
    class MigrationController extends ControllerBase {
        public function create( $name, $env ) {
            $GLOBALS[ 'env' ] = $env;

            require_once 'models/migration/base.php';

            try {
                require_once 'database/migration/' . $name;
            }
            catch ( MigrationException $e ) {
                throw $e;
            }
            echo "The migration script completed successfully without errors.\n"; 
        }
        public function createView() {
            require_once 'models/migration/base.php';

            $migrations = Migration::findAll();
            require_once 'views/migration/create.php';
        }
    }
