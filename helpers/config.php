<?php
    function mergeKeys( $config, $configLocal ) {
        if ( !is_array( $config ) ) {
            return $configLocal;
        }
        foreach ( $config as $key => $value ) {
            if ( isset( $configLocal[ $key ] ) ) {
                $config[ $key ] = mergeKeys( $config[ $key ], $configLocal[ $key ] );
            }
        }
        return $config;
    }
    function getConfig( $env ) {
        $config = require 'config/config.php';
        if ( file_exists( 'config/config-local.php' ) ) {
            $configLocal = require 'config/config-local.php';
            $config = mergeKeys( $config, $configLocal );
        }
        $config = $config[ $env ];
        $config[ 'root' ] = getcwd();
        $config[ 'base' ] = dirname( $_SERVER[ 'SCRIPT_NAME' ] ) . '/';
        return $config;
    }
?>
