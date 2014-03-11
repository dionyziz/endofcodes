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
    function getConfig() {
        $config = require 'config/config.php';
        if ( file_exists( 'config/config-local.php' ) ) {
            $configLocal = require 'config/config-local.php';
            $config = mergeKeys( $config, $configLocal );
        }
        return $config;
    }
?>
