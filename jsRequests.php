<?php

    if ( isset( $_POST[ 'function' ] ) ) {
        $func = new ReflectionFunction( $_POST[ 'function' ] );
        foreach( $func->getParameters() as $param ) {
            if ( isset( $_POST[ $param ] ) ) {
                $args[ $param ] = $_POST[ $param ];
            }
        }
        echo call_user_func_array( $_POST[ 'function' ], $args );
    }

    function checkUsername( $username = 'fdsafsa', $email ) {
        return $username;
    }
?>
