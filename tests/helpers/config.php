<?php
    class ConfigHelperTest extends UnitTest {
        public function setUp() {
            global $config;

            $this->tempConfigFile = 'tests/helpers/config-test.temp';
            touch( $this->tempConfigFile );
            $this->prototypeContent = file_get_contents( 'tests/helpers/formatConfig.prototype' );
            // Convert all newlines to \n.
            $this->prototypeContent = preg_replace( '/(\r\n|\r|\n)/', "\n", $this->prototypeContent );

            $this->localConfigPath = 'config/config-local.php';
            $this->originalLocalConfig = [];
            if ( file_exists( $this->localConfigPath ) ) {
                $this->originalLocalConfig = include $this->localConfigPath;
                $this->originalLocalConfigContents = file_get_contents( $this->localConfigPath );
            }
            $this->originalConfig = $config;
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
            $this->assertTrue( $loaded, 'The produced content must be valid, "includable" php code' );
            $this->assertSame( $someConfig, $loaded, 'The original config must be recovered successfully' );

            // The function seems to be working but we are also going to check the formmating by comparing against a prototype file.
            $this->assertSame( $this->prototypeContent, $output, 'The formating must be the same as in the prototype' );
        }
        public function testUpdateConfig() {
            global $config;

            $entries = [ 'some_entry' => 'some_value', 'some_array' => [ 'jpg', 'png' ] ];
            $environment = 'test';

            // Let's try to add some entries.
            updateConfig( $entries, $environment );
            $this->assertSame( $entries, array_diff_recursive( $config, $this->originalConfig ), 'The new entries must exist in the $config after calling updateConfig()' );
            $this->assertSame( [ $environment => $entries ], array_diff_recursive( include $this->localConfigPath, $this->originalLocalConfig ), 'The new entries must exist in the local-config.php after calling updateConfig()' );

            // And now let's try to remove them.
            $nullEntries = array_map( function( $entry ) {
                return NULL;
            }, $entries );
            updateConfig( $nullEntries, $environment );
            $this->assertSame( $this->originalConfig, $config, 'The entries that are set to NULL must be removed and $config must return to original state' );
            $this->assertSame( $this->originalLocalConfig, include $this->localConfigPath, 'The entries that are set to NULL must be removed and config-local.php must return to original state' );
        }
        public function tearDown() {
            global $config;

            unlink( $this->tempConfigFile );

            if ( isset( $this->originalLocalConfigContents ) ) {
                file_put_contents( $this->localConfigPath, $this->originalLocalConfigContents );
            }
            else if ( file_exists( $this->localConfigPath ) ) {
                unlink( $this->localConfigPath );
            }
            $config = $this->originalConfig;

        }
    }

    return new ConfigHelperTest();
?>
