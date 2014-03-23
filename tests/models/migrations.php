<?php
    require_once 'models/migration.php';

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
        public function testCreateLog() {
            //Migration::$path = 'database/migration/.migTestLog';
            Migration::createLog( 'migration1', 'development' ); 
            Migration::createLog( 'migration2', 'test' );
            Migration::createLog( 'migration3', 'test' );
            $logs = Migration::findLast();
            //$this->assertTrue( file_exists( Migration::$path ), 'The log file must exist' );
            //$this->assertEquals( 2,  count( $logs ), 'createLog should create only one record for each environment' );
            $this->assertTrue( array_key_exists( 'test', $logs ), 'The envs of last migrations must exist in the array' );
            $this->assertTrue( array_key_exists( 'development', $logs ), 'The envs of last migrations must exist in the array' );
            $this->assertEquals( $logs[ 'development' ], 'migration1' , 'Last migration name must have as a key its environment' );
            $this->assertEquals( $logs[ 'test' ], 'migration3', 'Last migration must have as a key its environment' );
        }
        public function testFindLast() {
            Migration::createLog( 'migration1', 'development' ); 
            Migration::createLog( 'migration2', 'test' );
            $logs = Migration::findLast();
            $logDev = Migration::findLast( 'development' );
            $logTest = Migration::findLast( 'test' );
            $this->assertTrue( is_array( $logs ) , 'findLast must return an array if not attributes are given' );
            $this->assertEquals( $logs[ 'development' ], 'migration1' , 'Last migration name must have as a key its environment' );
            $this->assertEquals( $logs[ 'test' ], 'migration2', 'Last migration must have as a key its environment' );
            $this->assertEquals( $logDev, 'migration1', 'findLast must return the value of the array with key its environment' );
            $this->assertEquals( $logTest, 'migration2', 'findLast must return the value of the array with key its environment' );
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
