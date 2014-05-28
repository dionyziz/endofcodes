<?php
    class FileHelperTest extends UnitTest {
        public function testSafeWrite() {
            $content = 'I shall be written safely';
            $filename = 'message.txt';
            safeWrite( $filename, $content );
            $read = file_get_contents( $filename );
            $this->assertEquals( $content, $read );

            chmod( $filename, 0444 ); //Read permission for everybody.
            $newContent = 'I have a bad premonition';
            $this->assertThrows(
                function() use ( $filename, $newContent ) {
                    safeWrite( $filename, $newContent );
                },
                'FileNotWritableException',
                'safeWrite() must throw an Exception when attempting to write a read-only file.',
                function( FileNotWritableException $e ) use ( $filename, $newContent ) {
                    $this->assertEquals( $filename, $e->filename );
                    $this->assertEquals( $newContent, $e->content );
                }
            );

            chmod( $filename, 0666 );
            unlink($filename);
        }
    }

    return new FileHelperTest();
?>
