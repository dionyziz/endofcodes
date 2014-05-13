<?php
    function isWholeNumber( $var ) {
      return ( is_numeric( $var ) && ( intval( $var ) == floatval( $var ) ) );
    }
?>
