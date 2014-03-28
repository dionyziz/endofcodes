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
            Migration::$path = 'tests/migrations/';
            Migration::$log = 'tests/migrations/.testLog';
            mkdir( Migration::$path, 0777, true );
            for ( $i = 1; $i <= 5; ++$i ) {
                file_put_contents( Migration::$path . $i . '.php', '' );
            }
        }
        public function tearDown() {
            try {
                Migration::dropTable( 'testTable' );
            }
            catch ( MigrationException $e ) {
            }
            $this->rrmdir( Migration::$path );
        }
        public function testMigrate() {
            $this->assertDoesNotThrow( function() {
                Migration::addField( 'testTable', 'test', 'int(11) NOT NULL' );
            }, 'MigrationException', 'migrate() must not throw a migration error when the script completes successfully' );
            $this->assertThrows( function() {
                Migration::addField( 'id', 'testTable', 'int(11) NOT NULL' ); 
            }, 'MigrationException', 'migrate() must throw a migration error when the script does not complete successfully' );
        }
        public function testCreateLog() {
            Migration::createLog( 'migration1', 'env1' ); 
            Migration::createLog( 'migration2', 'env2' );
            Migration::createLog( 'migration3', 'env2' );
            $logs = Migration::findLast();
            $this->assertTrue( file_exists( Migration::$path ), 'The log file must exist' );
            $this->assertEquals( count( $logs ), 2, 'createLog should create only one record for each environment' );
            $this->assertTrue( isset( $logs[ 'env1' ] ), 'The envs of last migrations must exist in the array' );
            $this->assertTrue( isset( $logs[ 'env2' ] ), 'The envs of last migrations must exist in the array' );
            $this->assertEquals( $logs[ 'env1' ], 'migration1', 'createLog must keep the last migration in each environment' );
            $this->assertEquals( $logs[ 'env2' ], 'migration3', 'createLog must keep the last migration in each environment' );
        }
        public function testFindLast() {
            Migration::createLog( 'migration1', 'env1' ); 
            Migration::createLog( 'migration2', 'env2' );
            $logs = Migration::findLast();
            $log1 = Migration::findLast( 'env1' );
            $log2 = Migration::findLast( 'env2' );
            $this->assertTrue( is_array( $logs ), 'findLast must return an array if not attributes are given' );
            $this->assertEquals( 'migration1', $logs[ 'env1' ], 'Last migration name must have a key its environment' );
            $this->assertEquals( 'migration2', $logs[ 'env2' ], 'Last migration must have a key its environment' );
            $this->assertEquals( 'migration1', $log1, 'findLast must return the value of the array with key its environment' );
            $this->assertEquals( 'migration2', $log2, 'findLast must return the value of the array with key its environment' );
            $this->assertThrows( function() {
                Migration::findLast( 'wrongEnv' ); 
            }, 'ModelNotFoundException', 'findLast() must throw a ModelNotFoundException when the environment is not isset in logs' );
        }
        public function testFindAll() {
            file_put_contents( Migration::$path . 'notphp.txt', '' );
            $all = Migration::findAll( Migration::$path );
            $this->assertTrue( is_array( $all ), 'findAll() must return an array' );
            $this->assertTrue( !in_array( 'notphp.txt', $all ), 'findAll() must return only php files' );
            $this->assertEquals( count( $all ), 5, 'findAll() must hold every php file in the directory' );
        }
        public function testFindUnexecuted() {
            Migration::$environments = [ 'env1', 'env2' ];
            Migration::createLog( '3.php', 'env1' ); 
            Migration::createLog( '2.php', 'env2' ); 
            $unex1 = Migration::findUnexecuted( 'env1' );
            $unex = Migration::findUnexecuted();
            file_put_contents( Migration::$log, '' );
            $all = Migration::findUnexecuted();
            $this->assertTrue( is_array( $unex1 ), 'findUnexecuted() must return an array' );
            $this->assertTrue( !in_array( '1.php', $unex1 ), 'findUnexecuted() must not return executed files' );
            $this->assertTrue( !in_array( '2.php', $unex1 ), 'findUnexecuted() must not return executed files' );
            $this->assertTrue( !in_array( '3.php', $unex1 ), 'findUnexecuted() must not return executed files' );
            $this->assertTrue( in_array( '4.php', $unex1 ), 'findUnexecuted() must return unexecuted files' );
            $this->assertTrue( in_array( '5.php', $unex1 ), 'findUnexecuted() must return unexecuted files' );
            $this->assertTrue( isset( $unex[ 'env1' ] ), 'findUnexecuted() must return all environments when not attributes are given' );
            $this->assertTrue( isset( $unex[ 'env2' ] ), 'findUnexecuted() must return all environments when not attributes are given' );
            $this->assertEquals( count( $all[ 'env1' ] ), 5, 'findUnexecuted() must return all files when not any log exists' );
            $this->assertEquals( count( $all[ 'env2' ] ), 5, 'findUnexecuted() must return all files when not any log exists' );
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
                Migration::dropField( 'test', 'test' );
            }, 'MigrationException', 'dropField must return an error when table not exists' );
            $this->assertFalse( in_array( 'test', $fields ), 'the thrown field should not exist in the list of the fields' );
        }
    }
    return new MigrationsTest();
?>
