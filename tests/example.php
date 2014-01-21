<?php
    // pass, see this?yes, if we had a wrong test...
    function assertTrue( $condition, $description = '' ) {
        if ( !$condition ) {
            throw new UnitTestFailedException( $description );
        }
    }

    class UnitTestFailedException extends Exception {}

    assertTrue( 3 + 2 == 5, '3 + 2 must equal 5' );
    assertTrue( 1 + 1 != 3, '1 + 1 must not equal 3' );
    assertTrue( 1 + 1 == 3, '1 + 1 must not equal 3' );
?>
