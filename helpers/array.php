<?php
    // Returns the elements of $minuend that are not elements of $subtrahend.
	// This extends array_diff_assoc() to multidimentional arrays.
	//
    // If any elements of $minuend are set to NULL they are not included in the $difference.
	// This enables us to use $array = array_diff_recursive( $array ) to remove NULL elements from an array.
    function array_diff_recursive( $minuend, $subtrahend = [] ) {
        $difference = [];
        foreach ( $minuend as $key => $value ) {
            if ( $value === NULL ) {
                continue;
            }
            if ( is_array( $value ) && $value !== [] ) {
                if ( !isset( $subtrahend[ $key ] ) ) {
                    $subtrahend[ $key ] = [];
                }
                $result = array_diff_recursive( $value, $subtrahend[ $key ] );
                if ( $result !== [] ) {
                    $difference[ $key ] = $result;
                }
            }
            else if ( !isset( $subtrahend[ $key ] ) || $value !== $subtrahend[ $key ] ) {
                $difference[ $key ] = $value;
            }
        }
        return $difference;
    }
?>