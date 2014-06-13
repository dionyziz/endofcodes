<?php
    class MigrationRunController extends ControllerBase {
        public function create( $environment, $name = '' ) {
            global $config;

            require_once 'models/migration.php';

            try {
                if ( $name != '' ) {
                    $migrations = [ $name ];
                }
                else {
                    $migrations = Migration::findUnexecuted( $environment );
                }

                foreach ( $migrations as $name ) {
                    $this->run( $name, $environment );
                }
            }
            catch ( FileNotReadableException $e ) {
                // ERROR
            }
            catch ( FileNotWritableException $e ) {
                // ERROR
            }

            require 'views/migration/results.php';
        }
        public function createView() {
            require_once 'models/migration.php';

            try {
                $environments = Migration::$environments;
                $allMigrations = Migration::findAll();
                $lastMigrationRun = Migration::loadLog();

                $pending = [];
                foreach ( Migration::$environments as $environment ) {
                    $pending[ $environment ] = Migration::findUnexecuted( $environment );
                }
            }
            catch ( FileNotReadableException $e ) {
                // ERROR
            }
            require 'views/migration/create.php';
        }
        protected function run( $name, $env ) {
            $migrations = Migration::findAll();
            $this->environment = $env;
            $this->getConfig();
            $this->dbInit(); // Does this works?
            if ( !in_array( $name, $migrations ) ) {
                throw new HTTPNotFoundException( 'No such migration (name = "' . $name . '")' );
            }
            require_once 'database/migration/' . $name;
            Migration::updateLog( $name, $env );
        }
    }
?>
