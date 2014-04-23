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
    function getBase() { 
        $protocol = 'http';
        if ( !empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
            $protocol .= 's';
        }
        $base =  $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . dirname( $_SERVER[ 'SCRIPT_NAME' ] );
        if ( substr( $config[ 'base' ], -1 ) != '/' ) {
            $base .= '/';
        }
        return $base;
    }
    function getConfig( $env ) {
        $config = require 'config/config.php';
        if ( file_exists( 'config/config-local.php' ) ) {
            $configLocal = require 'config/config-local.php';
            $config = mergeKeys( $config, $configLocal );
        }
        $config = $config[ $env ];
        $config[ 'root' ] = getcwd();
        $config[ 'base' ] = getBase();
        return $config;
    }
?>
