<?php
    require_once 'models/migration/base.php';

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
        public function setUp() {
            $this->createTable();        
        }
        public function tearDown() {
            try {
                Migration::dropTable( 'testTable' );
            }
            catch ( MigrationException $e ) {
            }
        }
        public function testCreateTable() {
            $tables = dbListTables(); 
            $this->assertDoesNotThrow( function () {
                $this->createTable();
            }, 'MigrationException' ); 
            $this->assertTrue( in_array( 'testTable', $tables ), 'testTable should exist in the list of the tables' );
        }
        public function testAddField() {
            Migration::addField( 
                'testTable', 
                'testfield', 
                'int(11) NOT NULL' 
            ); 
            $fields = dbListFields( 'testTable' );
            $this->assertDoesNotThrow( function () { 
                Migration::addField( 'testTable', 'test', 'int(11) NOT NULL' );
            }, 'MigrationException', 'addField must add a field when called' );
            $this->assertTrue( in_array( 'testfield', $fields ), 'testField should exist in the list of the fields' );
            $this->assertThrows( function() {
                Migration::addField( 'test', 'testfield1', 'int(11) NOT NULL' ); 
            }, 'MigrationException', 'addField must return an error when the table does not exist' );
        }
        public function testAlterField() {
            Migration::addField( 
                'testTable', 
                'testold',
                'int(11) NOT NULL'
            );
            $this->assertDoesNotThrow( function () { 
                Migration::alterField( 'testTable', 'testold', 'testnew', 'int(11) NOT NULL' );
            }, 'MigrationException', 'alterField must alter a field when called' );
            $fields = dbListFields( 'testTable' );
            $this->assertThrows( function () { 
                Migration::alterField( 'testTable', 'test', 'int(11) NOT NULL AUTO_INCREMENT' );
            }, 'MigrationException', 'alterField must not create a field when an attribute is missing' );
            $this->assertThrows( function () { 
                Migration::alterField( 'test', 'testold', 'testnew', 'int(11) NOT NULL AUTO_INCREMENT' );
            }, 'MigrationException', 'alterField must return an error when the table not exists' );  
            $this->assertFalse( in_array( 'testold', $fields ), 'the old field should not exist in the list of the fields' );
            $this->assertTrue( in_array( 'testnew', $fields ), 'the renamed field should exist in the list of the fields' );
        }
        public function testDropField() {
            Migration::addField( 
                'testTable', 
                'testfield',
                'int(11) NOT NULL'
            );
            $fields = dbListFields( 'testTable' );
            $this->assertDoesNotThrow( function() {
                Migration::dropField( 'testTable', 'testfield' );
            }, 'MigrationException', 'dropField must drop a field when called' );
            $this->assertThrows( function() {
                Migration::dropField( 'testTable' );
            }, 'MigrationException', 'dropField must return an error when an attribute is missing' );
            $this->assertThrows( function() {
                Migration::dropField( 'test', 'test' );
            }, 'MigrationException', 'dropField must return an error when table not exists' );
            $this->assertFalse( in_array( 'test', $fields ), 'the thrown field should not exist in the list of the fields' );
        }
        public function testAddFieldNoFieldName()  {
            $this->assertThrows( function() {
                Migration::addField( 'testTable' ); 
            }, 'MigrationException', 'addField must not create a field when fieldname is empty' );
        }
        public function testCreateTableNoFields() {
            $this->assertThrows( function() {
                Migration::createTable( 
                    'test', 
                    [],
                    []
                );
            }, 'MigrationException', 'createTable must not create a table when field are empty' );
        }
    }

    return new MigrationsTest();
?>
