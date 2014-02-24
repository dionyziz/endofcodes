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
            $trueSuccess = true;
            $emptySuccess = $noTableSuccess = false;
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
            Migration::dropField( 'testTable', 'testField' );
            ob_get_clean();
            $this->assertTrue( $trueSuccess, 'createField must add a field when called' );
            $this->assertTrue( $emptySuccess, 'createField must not create a field when fieldname is empty' );
            $this->assertTrue( $noTableSuccess, 'createField must return an error when table not exists' );
        }
        public function testAlterField() {
            ob_start();
            Migration::addField( 
                'testTable', 
                'field',
                'int(11) NOT NULL'
            );
            $trueSuccess = true;
            $syntaxSuccess = $noTableSuccess = false;
            try {
                Migration::alterField( 'testTable', 'field', 'testfield', 'int(11) NOT NULL' );
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false;
            }
            try {
                Migration::alterField( 'testTable', 'testfield', 'int(11) NOT NULL AUTO_INCREMENT' );
            }
            catch ( MigrationException $e ) {
                $syntaxSuccess = true;
            }
            try {
                Migration::alterField( 'test', 'testfield', 'newfield', 'int(11) NOT NULL AUTO_INCREMENT' );
            }
            catch ( MigrationException $e ) {
                $noTableSuccess = true;
            }
            ob_get_clean();
            $this->assertTrue( $trueSuccess, 'alterField must alter a field when called' );
            $this->assertTrue( $syntaxSuccess, 'alterField must not create a field when an attribute is missing' );
            $this->assertTrue( $noTableSuccess, 'alterField must return an error when table not exists' );
        }
        public function testDropField() {
            ob_start();
            Migration::addField( 
                'testTable', 
                'field',
                'int(11) NOT NULL'
            );
            $trueSuccess = true;
            $syntaxSuccess = $noTableSuccess = false;
            try {
                Migration::dropField( 'table' );
            }
            catch ( MigrationException $e ) {
                $syntaxSuccess = true;
            }
            try {
                Migration::dropField( 'table', 'field' );
            }
            catch ( MigrationException $e ) {
                $noTableSuccess = true;
            }
            try {
                Migration::dropField( 'testTable', 'field' );
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false; 
            }
            ob_get_clean();
            $this->assertTrue( $trueSuccess, 'dropField must drop a field when called' );
            $this->assertTrue( $syntaxSuccess, 'dropField must return an error when an attribute is missing' );
            $this->assertTrue( $noTableSuccess, 'dropField must return an error when table not exists' );
        }
    }
    return new MigrationsTest();
?>
