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
?>
