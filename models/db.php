<?php
    function db( $sql, $bind = [] ) {
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
        $res = mysql_query( $finalsql );
        if ( $res === false ) {
            throw new DBException( mysql_error() );
        }
        return $res;
    }

    function dbInsert( $table, $set ) {
        if ( empty( $set ) ) {
            $setString = ' () VALUES ()';
        }
        else {
            $fields = [];
            foreach ( $set as $field => $value ) {
                $fields[] = "$field = :$field";
            }
            $setString = ' SET ' . implode( ",", $fields );
        }
        $res = db(
            'INSERT INTO '
            . $table
            . $setString,
            $set
        );
        return mysql_insert_id();
    }

    function dbDelete( $table, $where ) {
        $fields = [];
        foreach ( $where as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        db(
            'DELETE FROM '
            . $table
            . ' WHERE '
            . implode( " AND ", $fields ),
            $where
        );
        return mysql_affected_rows();
    }

    function dbSelect( $table, $select = [ "*" ], $where = [], $orderBy = false, $limit = false ) {
        $fields = [];
        foreach ( $where as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        $sql =  'SELECT ' . implode( ",", $select ) . ' FROM ' . $table;
        if ( !empty( $where ) ) {
            $sql .= ' WHERE ' . implode( " AND ", $fields );
        }
        if ( $orderBy !== false ) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        if ( $limit !== false ) {
            $sql .= ' LIMIT ' . $limit;
        }
        return dbArray(
            $sql,
            $where
        );
    }

    function dbSelectOne( $table, $select = [ "*" ], $where = [] ) {
        $array = dbSelect( $table, $select, $where );
        if ( count( $array ) > 1 ) {
            throw new DBException( 'select one with multiple results' );
        }
        else if ( count( $array ) < 1 ) {
            throw new DBException( 'select one with no results' );
        }
        return $array[ 0 ];
    }

    function dbUpdate( $table, $set, $where ) {
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
        db(
            'UPDATE '
            . $table
            . ' SET '
            . implode( ",", $sfields )
            . ' WHERE '
            . implode( " AND ", $wfields ),
            array_merge( $wreplace, $sreplace )
        );
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

    class DBException extends Exception {
        public function __construct( $error ) {
            parent::__construct( 'Database error: ' . $error );
        }
    }
?>
