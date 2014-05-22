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
            $catched = false;
            try {
                safeWrite( $filename, $newContent );
            }
            catch ( FileNotWritableException $e ) {
                $catched = true;
                $this->assertEquals( $filename, $e->filename );
                $this->assertEquals( $newContent, $e->content );
            }
            $this->assertTrue( $catched );

            chmod( $filename, 0666 );
            unlink($filename);
        }
    }

    return new FileHelperTest();
?>