<?php
    interface DebuggerInterface {
        public function beginQueryExecution( DebuggerQuery $query );
        public function finishQueryExecution();
    }

    class DummyDebugger implements DebuggerInterface {
        public $queryGroups = [];

        public function beginQueryExecution( DebuggerQuery $query ) {}
        public function finishQueryExecution() {}
    }

    class Debugger implements DebuggerInterface {
        public $queryGroups = [];
        protected $currentQuery;

        public function beginQueryExecution( DebuggerQuery $query ) {
            $this->currentQuery = $query;
        }
        public function finishQueryExecution() {
            $this->currentQuery->finish();

            if ( !isset( $this->queryGroups[ $this->currentQuery->queryLiteral ] ) ) {
                $this->queryGroups[ $this->currentQuery->queryLiteral ] = new DebuggerQueryGroup( $this->currentQuery->queryLiteral );
            }
            $this->queryGroups[ $this->currentQuery->queryLiteral ]->add( $this->currentQuery );
        }
        public function getTotalQueriesExecuted() {
            $total = 0;
            foreach ( $this->queryGroups as $queryGroup ) {
                $total += count( $queryGroup->queries );
            }
            return $total;
        }
    }

    // represents a SQL query tied to debug metadata
    class DebuggerQuery {
        public $queryLiteral;
        public $params;
        protected $timeBegan = -1;
        public $timeTaken = -1;

        public function __construct( $queryLiteral, $params ) {
            $this->queryLiteral = $queryLiteral;
            $this->params = $params;
            $this->running = true;
            $this->timeBegan = microtime( true );
        }
        public function finish() {
            $this->running = false;
            $this->timeTaken = microtime( true ) - $this->timeBegan;
        }
    }

    // represents a group of queries with the same literal query but potentially different params
    class DebuggerQueryGroup {
        public $queryLiteral;
        public $queries = [];

        public function __construct( $queryLiteral ) {
            $this->queryLiteral = $queryLiteral;
        }
        public function add( DebuggerQuery $query ) {
            $this->queries[] = $query;
        }
        public function getTotalExecutionTime() {
            $sum = 0;
            foreach ( $this->queries as $query ) {
                $sum += $query->timeTaken;
            }
            return $sum;
        }
    }
?>
