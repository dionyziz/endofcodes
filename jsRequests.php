<?php
    if ( isset( $_POST[ 'function' ] ) ) {
        $args = get_func_argNames( $_POST[ 'function' ] );
        $params = [];
        foreach ( $args as $arg ) {
            if ( isset( $_POST[ $arg ] ) ) {
                $params[] = $_POST[ $arg ];  
            }
        }
        echo call_user_func_array( $_POST[ 'function' ], $params );
    }
    
    function checkUsername( $username ) {
        echo 'fdsa';
    }

    function get_func_argNames( $funcName ) {
        $func = new ReflectionFunction( $funcName );
        $result = array();
        foreach ( $func->getParameters() as $param ) {
            $result[] = $param->name;   
        }
        return $result;
    }
?>
