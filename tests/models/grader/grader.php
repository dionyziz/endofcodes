<?php
    class GraderTest extends UnitTestWithFixtures {
        public function testInitiate() {
            $bot1 = new GraderBot( $this->buildUser( 'vitsalis' ) );
            $bot2 = new GraderBot( $this->buildUser( 'pkakelas' ) );
            $grader = new Grader();
            $grader->bots = array( $bot1, $bot2 );
            $grader->initiate();
        }
    }
    return new GraderTest();
?>
