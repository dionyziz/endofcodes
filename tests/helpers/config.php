<?php
    class ConfigHelperTest extends UnitTest {
        public function setUp() {
            global $config;

            $this->tempConfigFile = 'tests/helpers/config-test.temp';
            touch( $this->tempConfigFile );
            $this->prototypeContent = file_get_contents( 'tests/helpers/formatConfig.prototype' );

            $this->localConfigPath = 'config/config-local.php';
            $this->originalLocalConfigContents = file_get_contents( $this->localConfigPath );
            $this->originalConfig = $config;
            $this->originalLocalConfig = [];
            if ( file_exists( 'config/config-local.php' ) ) {
                $this->originalLocalConfig = include $this->localConfigPath;
            }
        }
        public function testFormatConfig() {
            $someConfig = [
                'development' => [
                    'db' => [
                        'user' => 'endofcodes',
                        'pass' => 'sample_password',
                        'dbname' => 'endofcodes'
                    ]
                ]
            ];
            $output = formatConfig( $someConfig );

            file_put_contents( $this->tempConfigFile, $output );
            $loaded = require $this->tempConfigFile;
            $this->assertTrue( $loaded, 'The produced content must be valid, "includable" php code.' );
            $this->assertSame( $someConfig, $loaded, 'The original config must be recovered successfully.' );

            // The function seems to be working but we are also going to check the formmating by comparing against a prototype file.
            $this->assertEquals( $this->prototypeContent, $output, 'The formating must be the same as in the prototype.' );
        }
        public function testUpdateConfig() {
            global $config;

            $entries = [ 'some_entry' => 'some_value', 'some_array' => [ 'jpg', 'png' ] ];
            $environment = 'test';

            // Let's try to add some entries.
            updateConfig( $entries, $environment );
            $this->assertSame( $entries, array_diff_recursive( $config, $this->originalConfig ), 'The new entries must exist in the $config after calling updateConfig().' );
            $this->assertSame( [ $environment => $entries ], array_diff_recursive( include $this->localConfigPath, $this->originalLocalConfig ), 'The new entries must exist in the local-config.php after calling updateConfig().' );

            // And now let's try to remove them.
            $nullEntries = array_map( function( $entry ) {
                return NULL;
            }, $entries );
            updateConfig( $nullEntries, $environment );
            $this->assertSame( $this->originalConfig, $config, 'The entries that are set to NULL must be removed and $config must return to original state.' );
            $this->assertSame( $this->originalLocalConfig, include $this->localConfigPath, 'The entries that are set to NULL must be removed and config-local.php must return to original state.' );
        }
        public function tearDown() {
            global $config;

            unlink( $this->tempConfigFile );

            $config = $this->originalConfig;
            file_put_contents( $this->localConfigPath, $this->originalLocalConfigContents );
        }
    }

    return new ConfigHelperTest();
?>
