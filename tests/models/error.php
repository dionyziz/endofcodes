<?php
    include_once 'models/grader/error.php';

    class ErrorTest extends UnitTestWithFixtures {
        public function testCreate() {
            $game = $this->buildGame();
            $user = $this->buildUser( 'vitsalis' );
            $error = new Error( $game->id, $user->id, 'error' );
            $error->save();

            $errorObjects = Error::findErrorsByGameAndUser( $game->id, $user->id );
            
            $this->assertTrue( !empty( $errorObjects ), 'An error must be created in the database' );

            $this->assertEquals( 1, count( $errorObjects ), 'The number of errors in the database must be the same as the ones created' );

            $this->assertEquals( 'error', $errorObjects[ 0 ]->error, 'The error in the database must be the one during creation' );
        }
    }

    return new ErrorTest();
?>
