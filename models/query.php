<?php
    function prep_query( $code, $data = array() ) {
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
    }
?>
