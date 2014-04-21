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

        $protocol = 'http';
        if ( !empty( $s[ 'HTTPS' ] ) && $s[ 'HTTPS' ] == 'on' ) {
            $protocol .= 's';
        }
        $index_path = $index_file = $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'SCRIPT_NAME' ];
        $config[ 'base' ] = dirname( $index_path );
        if ( substr( $config[ 'base' ], -1 ) != '/' ) {
            $config[ 'base' ] .= '/';
        }

        return $config;
    }
?>
