<?php
    class MigrationRunController extends ControllerBase {
        public function create( $env, $name = '', $all = false ) {
            global $config;

            require_once 'models/migration.php';

            if ( !empty( $name ) ) {
                $migrations = [ $name ];
            }
            else {
                if ( $all ) {
                    $migrations = Migration::findAll();
                }
                else {
                    $migrations = Migration::findUnexecuted( $env );
                }
            }
            foreach ( $migrations as $name ) {
                $this->run( $name, $env ); 
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
            $pending = Migration::findUnexecuted();
            $migrations = Migration::findAll();
            require_once 'views/migration/create.php';
        }
        protected function run( $name, $env ) {
            $migrations = Migration::findAll();
            $this->environment = $env;
            $this->init();
            if ( !in_array( $name, $migrations ) ) {
                throw new HTTPNotFoundException( 'No such migration (name = "' . $name . '")' );
            }
            require_once 'database/migration/' . $name;
            Migration::createLog( $name, $env );
        }
    }
?>
