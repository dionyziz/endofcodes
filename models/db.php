<?php
    function dbInit() {
        global $config;

        $dbConnected = @mysql_connect(
            $config[ 'db' ][ 'host' ],
            $config[ 'db' ][ 'user' ],
            $config[ 'db' ][ 'pass' ]
        );
        if ( !$dbConnected ) {
            throw new DBException( 'Failed to connect to MySQL.', mysql_error() );
        }

        $dbSelected = @mysql_select_db( $config[ 'db' ][ 'dbname' ] );
        if ( !$dbSelected ) {
            throw new DBException( 'Failed to select MySQL database.', mysql_error() );
        }
    }
    function db( $sql, $bind = [] ) {
        global $debugger;

        foreach( $bind as $key => $value ) {
            if ( is_string( $value ) ) {
                $value = mysql_real_escape_string( $value );
                $value = '"' . $value . '"';
            }
            else if ( is_array( $value ) ) {
                foreach ( $value as $i => $subvalue ) {
                    $value[ $i ] = addslashes( $subvalue );
                }
                $value = "( '" . implode( "', '", $value ) . "' )";
            }
            else if ( is_null( $value ) ) {
                $value = '""';
            }
            $bind[ ':' . $key ] = $value;
            unset( $bind[ $key ] );
        }
        $finalsql = strtr( $sql, $bind );

        $debugger->beginQueryExecution( new DebuggerQuery( $sql, $bind ) );
        $result = mysql_query( $finalsql );
        $debugger->finishQueryExecution();

        if ( $result === false ) {
            throw new DBException( 'Failed to execute query', mysql_error() );
        }
        return $result;
    }

    function dbInsert( $table, $row ) {
        return dbInsertMulti( $table, [ $row ] );
    }

    function dbInsertMulti( $table, $rows ) {
        if ( empty( $rows ) ) {
            // nothing to do here
            return;
        }
        if ( empty( $rows ) ) {
            $setString = ' () VALUES ()';
        }
        else {
            $firstRow = $rows[ 0 ];
            $keys = '(' . implode( ',', array_keys( $firstRow ) ) . ')';
            $values = [];
            $i = 0;
            $bind = [];
            foreach ( $rows as $row ) {
                $valuePlaceHolders = [];
                if ( count( $row ) ) {
                    foreach ( $row as $key => $value ) {
                        ++$i;
                        $valuePlaceHolders[] = ":value$i";
                        $bind[ "value$i" ] = $value;
                    }
                }
                $values[] = "(" . implode( ",", $valuePlaceHolders ) . ")";
            }
            $setString = $keys . ' VALUES ' . implode( ",", $values );
        }
        $res = db(
            'INSERT INTO '
            . $table
            . $setString,
            $bind
        );
        return mysql_insert_id();
    }

    function dbDelete( $table, Array $where = [] ) {
        $fields = [];
        foreach ( $where as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        $sql = 'DELETE FROM ' . $table;
        if ( count( $where ) ) {
            $sql .= ' WHERE ' . implode( " AND ", $fields );
        }
        db( $sql, $where );
        return mysql_affected_rows();
    }

    function dbSelect( $table, $select = [ "*" ], $where = [], $orderBy = false, $limit = false ) {
        if ( empty( $where ) ) {
            return dbSelectMulti( $table, $select, [], $orderBy, $limit );
        }
        return dbSelectMulti( $table, $select, [ $where ], $orderBy, $limit );
    }

    function dbSelectMulti( $table, $select = [ "*" ], $wheres = [], $orderBy = false, $limit = false ) {
        $sql =  'SELECT ' . implode( ",", $select ) . ' FROM ' . $table;
        $bind = [];
        if ( !empty( $wheres ) ) {
            $firstWhere = $wheres[ 0 ];
            $keys = '(' . implode( ',', array_keys( $firstWhere ) ) . ')';
            $in = [];
            $i = 0;
            foreach ( $wheres as $where ) {
                $inHolder = [];
                if ( count( $where ) ) {
                    foreach ( $where as $key => $value ) {
                        ++$i;
                        $inHolder[] = ":value$i";
                        $bind[ "value$i" ] = $value;
                    }
                }
                $in[] = '(' . implode( ",", $inHolder ) . ')';
            }
            $sql = $sql . ' WHERE ' . $keys . ' IN ( ' . implode( ",", $in ) . ')';
        }
        if ( $orderBy !== false ) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        if ( $limit !== false ) {
            assert( $limit > 0/*, 'limit must be a positive integer'*/ );
            $sql .= ' LIMIT ' . $limit;
        }
        return dbArray(
            $sql,
            $bind
        );
    }

    function dbSelectOne( $table, $select = [ "*" ], $where = [] ) {
        $array = dbSelect( $table, $select, $where );
        if ( count( $array ) > 1 ) {
            throw new DBExceptionWrongCount( 1, 2 );
        }
        else if ( count( $array ) < 1 ) {
            throw new DBExceptionWrongCount( 1, 0 );
        }
        return $array[ 0 ];
    }

    function dbUpdate( $table, Array $set = [], Array $where = [] ) {
        if ( empty( $set ) ) {
            // nothing to do
            return;
        }

        $wfields = [];
        $wreplace = [];
        foreach ( $where as $field => $value ) {
            $wfields[] = "$field = :where_$field";
            $wreplace[ 'where_' . $field ] = $value;
        }
        $sfields = [];
        $sreplace = [];
        foreach ( $set as $field => $value ) {
            $sfields[] = "$field = :set_$field";
            $sreplace[ 'set_' . $field ] = $value;
        }
        $sql = 'UPDATE ' . $table;
        $sql .= ' SET ' . implode( ",", $sfields );
        if ( !empty( $where ) ) {
            $sql .= ' WHERE ' . implode( " AND ", $wfields );
        }
        db( $sql, array_merge( $wreplace, $sreplace ) );

        return mysql_affected_rows();
    }

    function dbArray( $sql, $bind = [], $id_column = false ) {
        $res = db( $sql, $bind );
        $rows = [];
        if ( $id_column !== false ) {
            while ( $row = mysql_fetch_array( $res ) ) {
                $rows[ $row[ $id_column ] ] = $row;
            }
        }
        else {
            while ( $row = mysql_fetch_array( $res ) ) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    function dbListTables() {
        return array_map( 'array_shift', dbArray( 'SHOW TABLES' ) );
    }

    function dbListFields( $table ) {
        return array_map( 'array_shift', dbArray( "SHOW COLUMNS FROM $table" ) );
    }

    class DBException extends Exception {
        public $error;
        public $dbSaid;
        public function __construct( $error, $dbSaid = '' ) {
            $this->error = $error;
            $this->dbSaid = $dbSaid;
            $message = 'Database error: ' . $error;
            if ( !empty( $dbSaid ) ) {
                $message .= ' MySQL said: ' . $dbSaid;
            }
            parent::__construct( $message );
        }
    }

    class DBExceptionWrongCount extends DBException {
        public $expected;
        public $actual;

        public function __construct( $expected, $actual ) {
            $this->expected = $expected;
            $this->actual = $actual;
            parent::__construct( "Database error: Expected $expected rows to be returned, but $actual were returned." );
        }
    }

    class MigrationException extends Exception {
        public function __construct( $error ) {
            parent::__construct( 'Migration error: ' . $error );
        }
    }
?>
