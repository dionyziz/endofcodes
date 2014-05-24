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
            $oldLocalConfig = include 'config/config-local.php';

            $entries = [ 'some_entry' => 'some_value' ];
            $environment = 'test';
            updateConfig( $entries, $environment );

            $this->assertTrue( array_diff_recursive( $config, $oldConfig ) == $entries );

            $localConfig = include 'config/config-local.php';
            $this->assertTrue( array_diff_recursive( $localConfig, $oldLocalConfig ) == [ $environment => $entries ] );

            updateConfig( [ 'some_entry' => '' ], $environment ); // Clean up.
            $this->assertTrue( $oldConfig == $config );

            $localConfig = include 'config/config-local.php';
            $this->assertTrue( $oldLocalConfig == $localConfig );
        }
    }

    return new ConfigHelperTest();
?>
