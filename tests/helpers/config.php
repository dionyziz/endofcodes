<?php
    class ConfigHelperTest extends UnitTest {
        public function setUp() {
            echo 'test0';
            $this->config = [
                'development' => [
                    'db' => [
                        'user' => 'endofcodes',
                        'pass' => 'sample_password',
                        'dbname' => 'endofcodes'
                    ]
                ]
            ];
            $this->tempConfigFile = 'tests/helpers/config-test.temp';
            touch( $this->tempConfigFile );
            file_put_contents( 'tests/helpers/formatConfig.prototype', formatConfig($this->config) );
            $this->prototypeContent = file_get_contents( 'tests/helpers/formatConfig.prototype' );
        }
        public function testFormatConfig() {
            echo 'test1';
            $output = formatConfig( $this->config );

            file_put_contents( $this->tempConfigFile, $output );
            $loaded = require $this->tempConfigFile;
            $this->assertTrue( $loaded, 'The produced content must be valid, "includable" php code.' );
            $this->assertSame( $this->config, $loaded, 'The original config must be recovered successfully.' );

            // The function seems to be working but we are also going to check the formmating by comparing against a prototype file.
            $this->assertEquals( $this->prototypeContent, $output, 'The formating must be the same as in the prototype.' );
        }
        public function testUpdateConfig() {
            global $config;
            echo 'test2';
            $oldConfig = $config;
            $oldLocalConfig = [];
            if ( file_exists( 'config/config-local.php' ) ) {
                $oldLocalConfig = include 'config/config-local.php';
            }

            $entries = [ 'some_entry' => 'some_value', 'some_array' => [ 'jpg', 'png' ] ];
            $environment = 'test';

            updateConfig( $entries, $environment );
            $this->assertEquals( $entries, array_diff_recursive( $config, $oldConfig ) );

            $localConfig = include 'config/config-local.php';
            $this->assertEquals( [ $environment => $entries ], array_diff_recursive( $localConfig, $oldLocalConfig ) );

            $nullEntries = array_map( function( $entry ) {
                return NULL;
            }, $entries);
            updateConfig( $nullEntries, $environment ); // Clean up.
            $this->assertEquals( $oldConfig, $config );

            $localConfig = include 'config/config-local.php';
            $this->assertEquals( $oldLocalConfig, $localConfig );
        }
        public function tearDown() {
            echo 'test-1';
            unlink( $this->tempConfigFile );
        }
    }

    return new ConfigHelperTest();
?>
