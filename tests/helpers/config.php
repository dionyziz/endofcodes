<?php
    class ConfigHelperTest extends UnitTest {
        public function testFormatConfig() {
            $config = [
                'development' => [
                    'db' => [
                        'user' => 'endofcodes',
                        'pass' => 'sample_password',
                        'dbname' => 'endofcodes'
                    ]
                ]
            ];
            $output = formatConfig( $config );

            $filename = 'config-test.php';
            file_put_contents( $filename, $output );
            $loaded = require $filename;
            unlink( $filename );

            $this->assertTrue( $loaded == $config );
        }
        public function testUpdateConfig() {
            global $config;

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
    }

    return new ConfigHelperTest();
?>
