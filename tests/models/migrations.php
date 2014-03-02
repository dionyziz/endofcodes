<?php
    require_once 'database/migration/migrate.php';

    class MigrationsTest extends UnitTestWithFixtures {
        protected function createTable() {
            Migration::createTable( 
                'testTable', 
                [
                    'id' => 'int(11) NOT NULL AUTO_INCREMENT'
                ],
                [   
                    [ 'type' => 'primary', 'field' => 'id' ]
                ]
            );
        }
        public function testCreateTable() {
            $trueSuccess = true;
            try { 
                $this->createTable();
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false; 
            }
            $tables = dbListTables(); 
            if ( !in_array( 'testTable', $tables ) ) {
                $trueSuccess = false; 
            }
            $this->assertTrue( $trueSuccess, 'createTable must create a table when called' );
        }
        public function testAddField() {
            $this->createTable();
            $trueSuccess = true;
            $noTableSuccess = false;
            try {
                Migration::addField( 'testTable', 'test1', 'int(11) NOT NULL' ); 
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false; 
            }
            $fields = dbListFields( 'testTable' );
            if ( !in_array( 'test1', $fields ) ) {
                $trueSuccess = false; 
            }
            try {
                Migration::addField( 'test', 'testField', 'int(11) NOT NULL' ); 
            }
            catch ( MigrationException $e ) {
                $noTableSuccess = true; 
            }
            $this->assertTrue( $trueSuccess, 'addField must add a field when called' );
            $this->assertTrue( $noTableSuccess, 'addField must return an error when table not exists' );
        }
        public function testAlterField() {
            $this->createTable();
            Migration::addField( 
                'testTable', 
                'test2',
                'int(11) NOT NULL'
            );
            $trueSuccess = true;
            $syntaxSuccess = $noTableSuccess = false;
            try {
                Migration::alterField( 'testTable', 'test2', 'testnew2', 'int(11) NOT NULL' );
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false;
            }
            $fields = dbListFields( 'testTable' );
            if ( !in_array( 'testnew2', $fields ) ) {
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
            $this->assertTrue( $trueSuccess, 'alterField must alter a field when called' );
            $this->assertTrue( $syntaxSuccess, 'alterField must not create a field when an attribute is missing' );
            $this->assertTrue( $noTableSuccess, 'alterField must return an error when table not exists' );
        }
        public function testDropField() {
            $this->createTable();
            Migration::addField( 
                'testTable', 
                'test3',
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
                Migration::dropField( 'table', 'test3' );
            }
            catch ( MigrationException $e ) {
                $noTableSuccess = true;
            }
            try {
                Migration::dropField( 'testTable', 'test3' );
            }
            catch ( MigrationException $e ) {
                $trueSuccess = false; 
            }
            $fields = dbListFields( 'testTable' );
            if ( in_array( 'test3', $fields ) ) {
                $trueSuccess = false; 
            }
            $this->assertTrue( $trueSuccess, 'dropField must drop a field when called' );
            $this->assertTrue( $syntaxSuccess, 'dropField must return an error when an attribute is missing' );
            $this->assertTrue( $noTableSuccess, 'dropField must return an error when table not exists' );
        }
        public function testAddFieldNoFieldName()  {
            $this->createTable();
            $emptySuccess = false;
            try {
                Migration::addField( 'testTable' ); 
            }
            catch ( MigrationException $e ) {
                $emptySuccess = true; 
            }
            $this->assertTrue( $emptySuccess, 'addField must not create a field when fieldname is empty' );
        }
        public function testCreateTableNoFields() {
            $emptySuccess = false;
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
            $this->assertTrue( $emptySuccess, 'createTable must not create a table when field are empty' );
            $this->createTable();
        }
        public function tearDown() {
            Migration::dropTable( 'testTable' );
        }
    }

    return new MigrationsTest();
?>
