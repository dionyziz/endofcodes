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

                $this->resetDBEnvironment( $environment );
                foreach ( $migrations as $name ) {
                    $this->run( $name );
                }
                Migration::updateLog( end( $migrations ), $environment );
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
        protected function resetDBEnvironment( $environment ) {
            $this->environment = $environment;
            $this->getConfig();
            $this->dbInit(); // Does this work?
        }
        protected function run( $name ) {
            $migrations = Migration::findAll();

            if ( !in_array( $name, $migrations ) ) {
                throw new HTTPNotFoundException( 'No such migration (name = "' . $name . '")' );
            }
            require_once 'database/migration/' . $name;

        }
    }
?>
