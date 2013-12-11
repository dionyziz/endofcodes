<?php
    class DBException extends Exception {
        public function __construct() {
            parent::__construct( 'Database error: ' . mysql_error() );
        }
    }

    function db( $sql, $bind = array() ) {
        foreach( $bind as $key => $value ) {
            if ( is_string( $value ) ) {
                $value = addslashes( $value );
                $value = '"' . $value .'"';
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
            throw new DBException();
        }
        return $res;
    }

    function dbInsert( $table, $set ) {
        $fields = array();
        foreach ( $set as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        $res = db(
            'INSERT INTO '
            . $table
            . ' SET '
            . implode( ",", $fields ),
            $set
        );
        if ( $res == false ) {
            return $res;
        }
        return mysql_insert_id();
    }

    function dbDelete( $table, $where ) {
        $fields = array();
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

    function dbSelect( $table, $select = array( "*" ), $where = array() ) {
        $fields = array();
        foreach ( $where as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        $sql =  'SELECT ' . implode( ",", $select ) . ' FROM ' . $table;
        if ( !empty( $where ) ) {
            $sql = $sql . ' WHERE ' . implode( " AND ", $fields );
        }
        return dbArray(
            $sql,
            $where
        );
    }

    function dbSelectOne( $table, $select = array( "*" ), $where = array() ) {
        $array = array_pop( dbSelect( $table, $select, $where ) );
        if ( count( $array ) !== 1 ) {
            throw new DBException();
        }
        return $array;
    }

    function dbUpdate( $table, $set, $where ) {
        $wfields = array();
        $wreplace = array();
        foreach ( $where as $field => $value ) {
            $wfields[] = "$field = :where_$field";
            $wreplace[ 'where_' . $field ] = $value;
        }
        $sfields = array();
        $sreplace = array();
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

    function dbArray( $sql, $bind = false, $id_column = false ) {
        $res = db( $sql, $bind );
        $rows = array();
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
?>
