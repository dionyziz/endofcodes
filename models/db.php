<?php
    /*function prep_query( $code, $data = array() ) {
        $parts = explode( '?', $code );
        $sql = '';
        foreach( $data as $value ) {
            $sql .= array_shift( $parts );
            $sql .= '"' . mysql_real_escape_string( $value ) . '"';
        }
        $sql .= array_shift( $parts );
        $res = mysql_query( $sql );
        if ( $res !== false ) {
            return $res;
        }
        die( 'MySQL error: ' . mysql_error() );
    }*/
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
        /*if ( $res === false ) {
            die( "SQL query died with the following error\n\""
            . mysql_error()
            . "\"\n\nThe query given was:\n"
            . $sql
            . "\n\nThe SQL bindings were:\n"
            . print_r( $bind, true )
            . "The query executed was:\n"
            . $finalsql );
        }*/
        return $res;
    }

    function db_insert( $table, $set ) {
        $fields = array();
        foreach ( $set as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        db(
            'INSERT INTO '
            . $table
            . ' SET '
            . implode( ",", $fields ),
            $set
        );
        return mysql_insert_id();
    }

    function db_delete( $table, $where ) {
        $fields = array();
        foreach ( $where as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        db(
            'INSERT FROM '
            . $table
            . ' WHERE '
            . implode( " AND ", $fields ),
            $where
        );
        return mysql_affected_rows();
    }

    function db_select( $table, $select = array( "*" ), $where ) {
        $fields = array();
        foreach ( $where as $field => $value ) {
            $fields[] = "$field = :$field";
        }
        return db(
            'SELECT '
            . implode( ",", $select )
            . ' FROM '
            . $table
            . ' WHERE '
            . implode( " AND ", $fields ),
            $where
        );
    }

    function db_update( $table, $set, $where ) {
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
?>
