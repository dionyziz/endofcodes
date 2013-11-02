<?php
    function prep_query( $code, $data = array() ) {
        $parts = explode( '?', $code );
        $sql = '';
        foreach( $data as $value ) {
            $sql .= array_shift( $parts );
            $sql .= '"' . mysql_real_escape_string( $value ) . '"';
        }
        $sql .= array_shift( $parts );
        return mysql_query( $sql );
    }
?>
