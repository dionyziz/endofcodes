<?php
    class HTMLHelperTest extends UnitTestWithFixtures {
        public function testCreateSelectPrepare() {
            $test = [ 'a', 'b' ];
            $keys = [ 'key1', 'key2' ];
            
            $res1 = createSelectPrepare( $test ); 
            $this->assertTrue( is_array( $res1 ), 
                'createPrepareSelect must return an array with keys its elements if not any keys are given' );
            $this->assertTrue( isset( $res1[ 'a' ] ), 
                'createPrepareSelect must return an array with keys its elements if not any keys are given' );
            $this->assertEquals( $res1[ 'a' ], 'a',  
                'createPrepareSelect keys must be the same with elements if not any keys are given' );
            $this->assertTrue( isset( $res1[ 'b' ] ), 
                'createPrepareSelect must return an array with keys its elements if not any keys are given' );
            $this->assertEquals( $res1[ 'b' ], 'b',  
                'createPrepareSelect keys must be the same with elements if not any keys are given' );

            $res2 = createSelectPrepare( $test, '', $keys ); 
            $this->assertEquals( $res2[ 'key1' ], 'a',  
                'createPrepareSelect keys must be the same with elements if not any keys are given' );
            $this->assertEquals( $res2[ 'key2' ], 'b',  
                'createPrepareSelect must combine the array with the keys in order' );

            $res3 = createSelectPrepare( $test, 'testTitle' ); 
            $this->assertEquals( $res3[ 'title' ], 'testTitle',  
                'createPrepareSelect must hold in the key "first" the value given as a second parameter' );
            $this->assertEquals( array_shift( array_slice( $res3, 0, 1 ) ), 'testTitle',  
                'createPrepareSelect must return an array with first element the value given as a second parameter' );
        } 
    }

    return new HTMLHelperTest();
?>
