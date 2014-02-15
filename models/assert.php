<?php
    assert_options( ASSERT_ACTIVE, true );
    assert_options( ASSERT_BAIL, false );
    assert_options( ASSERT_WARNING, false );
    assert_options( ASSERT_CALLBACK, 'assertFailure' );

    function assertFailure( $script, $line, $_, $message ) {
        throw new AssertionFailureException( $script, $line, $message );
    }

    class AssertionFailureException extends Exception {
        public function __construct( $script, $line, $message ) {
            parent::__construct( "Assertion failed. File: $script. Line: $line. Message: $message" );
        }
    }
?>
