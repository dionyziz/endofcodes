<?php
    require_once 'models/error.php';

    class ErrorTest extends UnitTestWithFixtures {
        public function testCreate() {
            $game = $this->buildGame();
            $user = $this->buildUser( 'vitsalis' );
            $error = new Error();
            $error->game = $game;
            $error->user = $user;
            $error->description = 'description';
            $error->actual = 'actual';
            $error->expected = 'expected';
            $error->save();

            $errorObjects = Error::findErrorsByGameAndUser( $game->id, $user->id );
            
            $this->assertTrue( !empty( $errorObjects ), 'An error must be created in the database' );

            $this->assertEquals( 1, count( $errorObjects ), 'The number of errors in the database must be the same as the ones created' );

            $this->assertEquals( 'description', $errorObjects[ 0 ]->description, 'The description in the database must be the one during creation' );
            $this->assertEquals( 'actual', $errorObjects[ 0 ]->actual, 'The actual in the database must be the one during creation' );
            $this->assertEquals( 'expected', $errorObjects[ 0 ]->expected, 'The expected in the database must be the one during creation' );
        }
    }

    return new ErrorTest();
?>
