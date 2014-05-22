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
    }

    return new ConfigHelperTest();
?>
