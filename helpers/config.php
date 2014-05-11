<?php
    function getBase() { 
        $protocol = 'http';
        if ( !empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
            $protocol .= 's';
        }
        // All scripts are called by index.php so SCRIPT_NAME always points to index.php
        $relativePath = dirname( $_SERVER[ 'SCRIPT_NAME' ] );
        if ( substr( $relativePath, -1 ) != '/' ) {
            $relativePath .= '/';
        }
        if ( !isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
            // using CLI
            return '';
        }
        return $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $relativePath;
    }
    function getConfig( $env ) {
        $config = require 'config/config.php';
        if ( file_exists( 'config/config-local.php' ) ) {
            $configLocal = require 'config/config-local.php';
            $config = array_replace_recursive( $config, $configLocal );
        }
        $config = $config[ $env ];
        $config[ 'root' ] = getcwd();
        $config[ 'base' ] = getBase();
        return $config;
    }
?>
