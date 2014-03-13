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
    function getConfig( $migration = false ) {
        $pref = '';
        if ( $migration !== false ) {
            $pref = '../../';
        }
        $config = require $pref . 'config/config.php';
        if ( file_exists( $pref . 'config/config-local.php' ) ) {
            $configLocal = require $pref . 'config/config-local.php';
            $config = mergeKeys( $config, $configLocal );
        }
        $config[ 'root' ] = getcwd();
        return $config;
    }
?>
