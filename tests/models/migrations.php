<?php
    include_once 'database/migration/migrate.php';

    class MigrationsTest extends UnitTestWithFixtures {
        public function testCreateTable() {
            ob_start();
            $emptySuccess = false;
            $trueSuccess =true;
            try { 
                Migration::createTable( 
                    'testTable', 
                    [
                        'id' => 'int(11) NOT NULL AUTO_INCREMENT'
                    ],
                    [   
                        [ 'type' => 'primary', 'field' => [ 'id' ] ]
                    ]
                );
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false; 
            }
            try { 
                Migration::createTable( 
                    'testTable', 
                    [],
                    []
                );
            }
            catch ( MigrationException $e ) {
                $emptySuccess = true; 
            }
            ob_get_clean();
            $this->assertTrue( $trueSuccess, 'createTable must create a table when called' );
            $this->assertTrue( $emptySuccess, 'createTable must not create a table when field are empty' );
        }
        public function testCreateField() {
            ob_start();
            Migration::createTable( 
                'testTable', 
                [
                    'id' => 'int(11) NOT NULL AUTO_INCREMENT'
                ],
                [   
                    [ 'type' => 'primary', 'field' => [ 'id' ] ]
                ]
            );
            $emptySuccess = false;
            $trueSuccess = true;
            $noTableSuccess = false;
            try {
                Migration::addField( 'testTable', 'testField', 'int(11) NOT NULL' ); 
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false; 
            }
            try {
                Migration::addField( 'testTable' ); 
            }
            catch ( MigrationException $e ) {
                $emptySuccess = true; 
            }
            try {
                Migration::addField( 'test', 'testField', 'int(11) NOT NULL' ); 
            }
            catch ( MigrationException $e ) {
                $noTableSuccess = true; 
            }
            ob_get_clean();
            $this->assertTrue( $trueSuccess, 'createField must add a field when called' );
            $this->assertTrue( $emptySuccess, 'createField must not create a field when fieldname is empty' );
            $this->assertTrue( $noTableSuccess, 'createField must return an error when table not exists' );
        }
    }
    return new MigrationsTest();
?>
