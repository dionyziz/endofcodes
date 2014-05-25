<?php
    require_once 'models/debug.php';

    class DebuggerTest extends UnitTest {
        protected $queryLiteral;
        protected $params;

        protected function getQueryFixture() {
            $this->queryLiteral = 'SELECT meaning FROM life WHERE value = :value';
            $this->params = [ 'value' => 42 ];
            $query = new DebuggerQuery( $this->queryLiteral, $this->params );
            
            return $query;
        }
        public function testDebuggerQuery() {
            $query = $this->getQueryFixture();

            $this->assertEquals( $this->queryLiteral, $query->queryLiteral, 'queryLiteral attribute must be set appropriately on DebuggerQuery' );
            $this->assertEquals( $this->params, $query->params, 'params attribute must be set appropriately on DebuggerQuery' );
            $this->assertTrue( $query->running, 'An unfinished query must be marked as running' );

            $query->finish();

            $this->assertFalse( $query->running, 'A finished query must be marked as non-running' );
            $this->assertTrue( $query->timeTaken >= 0, 'Each query must record the time it took and it must be non-negative' );
        }
        public function testDebuggerQueryGroup() {
            $query = $this->getQueryFixture();
            $query->finish();
            $query->timeTaken = 1;

            $queryGroup = new DebuggerQueryGroup( $query->queryLiteral );

            $this->assertEquals( $query->queryLiteral, $queryGroup->queryLiteral, 'queryGroup must store the query literal' );

            $queryGroup->add( $query );
            $queryGroup->add( $query );

            $this->assertEquals( 2, $queryGroup->getTotalExecutionTime(), 'queryGroup must add individual query times' );
        }
        public function testDummyDebugger() {
            $debugger = new DummyDebugger();

            $this->assertTrue( is_array( $debugger->queryGroups ), 'DummyDebugger must offer trivial debugging capabilities by providing an array of queryGroups without contract' );
        }
        public function testDebugger() {
            $query = $this->getQueryFixture();

            $debugger = new Debugger();

            $debugger->beginQueryExecution( $query );
            $debugger->finishQueryExecution();

            $this->assertEquals( 1, count( $debugger->queryGroups ), 'Debugger must record each query exactly once' );
            $this->assertTrue( isset( $debugger->queryGroups[ $query->queryLiteral ] ), 'Debugger must record each query by literal' );

            $debugger->beginQueryExecution( $query );
            $debugger->finishQueryExecution();

            $this->assertEquals( 1, count( $debugger->queryGroups ), 'Debugger must group similar queries' );
            $this->assertEquals( 2, count( $debugger->queryGroups[ $query->queryLiteral ]->queries ), 'Debugger must add all queries to each group' );

            $differentQuery = new DebuggerQuery( 'SELECT restaurant FROM universe WHERE time = :end', [ 'end' => 'near' ] );
            $debugger->beginQueryExecution( $differentQuery );

            $this->assertEquals( 1, count( $debugger->queryGroups ), 'Debugger must not record a query before it finished executing' );

            $debugger->finishQueryExecution();

            $this->assertEquals( 2, count( $debugger->queryGroups ), 'Debugger must not group different queries together' );
            $this->assertEquals( 3, $debugger->getTotalQueriesExecuted(), 'Debugger must count queries from all groups' );
        }
    }

    return new DebuggerTest();
?>
