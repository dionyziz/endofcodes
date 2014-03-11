<?php
    require_once 'models/error.php';

    class ErrorTest extends UnitTestWithFixtures {
        public function testSaveDb() {
            $game = $this->buildGame();
            $user = $this->buildUser( 'vitsalis' );
            $error = $this->buildError( 'description', 'actual', 'expected', $user, $game );
            $dbError = new Error( $error->id );

            $this->assertSame( $error->id, $dbError->id, 'id must be correctly stored in the database' );
            $this->assertSame( $error->game->id, $dbError->game->id, 'gameid must be correctly stored in the database' );
            $this->assertSame( $error->user->id, $dbError->user->id, 'userid must be correctly stored in the database' );
            $this->assertSame( $error->description, $dbError->description, 'description must be correctly stored in the database' );
            $this->assertSame( $error->actual, $dbError->actual, 'actual must be correctly stored in the database' );
            $this->assertSame( $error->expected, $dbError->expected, 'expected must be correctly stored in the database' );
        }
        public function testfindErrorsByGameAndUser() {
            $game = $this->buildGame();
            $user = $this->buildUser( 'vitsalis' );
            $error = $this->buildError( 'description', 'actual', 'expected', $user, $game );

            $errorObjects = Error::findErrorsByGameAndUser( $game->id, $user->id );
            
            $this->assertTrue( !empty( $errorObjects ), 'An error must be created in the database' );

            $this->assertEquals( 1, count( $errorObjects ), 'The number of errors in the database must be the same as the ones created' );

            $this->assertEquals( $error->description, $errorObjects[ 0 ]->description, 'The description in the database must be the one during creation' );
            $this->assertEquals( $error->actual, $errorObjects[ 0 ]->actual, 'The actual in the database must be the one during creation' );
            $this->assertEquals( $error->expected, $errorObjects[ 0 ]->expected, 'The expected in the database must be the one during creation' );
        }
    }

    return new ErrorTest();
?>
