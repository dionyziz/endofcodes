<?php
    function getBase() {
        $protocol = 'http';
        if ( !empty( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
            $protocol .= 's';
        }
        // All scripts are called by index.php so SCRIPT_NAME always points to index.php.
        $relativePath = dirname( $_SERVER[ 'SCRIPT_NAME' ] );
        if ( substr( $relativePath, -1 ) != '/' ) {
            $relativePath .= '/';
        }
        if ( !isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
            // Using CLI.
            return '';
        }
        return $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $relativePath;
    }
    function getConfig( $environment ) {
        $config = require 'config/config.php';
        if ( file_exists( 'config/config-local.php' ) ) {
            $configLocal = require 'config/config-local.php';
            $config = array_replace_recursive( $config, $configLocal );
        }
        $config = $config[ $environment ];
        $config[ 'root' ] = getcwd();
        $config[ 'base' ] = getBase();
        return $config;
    }
    function formatConfig( $config ) {
        $content = '<?php' . PHP_EOL . 'return ' . var_export( $config, true ) . ';' . PHP_EOL . '?>';

        // Convert 2-space indentation to 4-space.
        $content = str_replace( '  ', '    ', $content );
        // Replace array(...) with [...].
        $content = preg_replace( '/\s*array \(/', ' [', $content );
        $content = str_replace( ')', ']', $content );
        // Indent code inside <?php tags.
        $content = preg_replace( '/(^[^(<)|(\?)])/m', '    $1', $content );

        return $content;
    }
    function array_diff_recursive( $minuend, $subtrahend = [] ) {
        $difference = [];
        if ( !is_array( $minuend ) ) {
            if ( $minuend != $subtrahend ) {
                $difference = $minuend;
            }
            return $difference;
        }
        foreach ( $minuend as $key => $value ) {
            if ( !isset( $subtrahend[ $key ] ) ) {
                $subtrahend[ $key ] = '';
            }
            $difference[ $key ] = array_diff_recursive( $value, $subtrahend[ $key ] );
            if ( empty( $difference[ $key ] ) ) {
                unset( $difference[ $key ] );
            }
        }
        return $difference;
    }
    function updateConfig( $entries, $environment ) {
        global $config;

        $entries = [ $environment => $entries ];
        $configPath = 'config/config-local.php';

        if ( file_exists( $configPath ) ) {
            $old_entries = include $configPath;
            $entries = array_replace_recursive( $old_entries, $entries );
        }
        $config = array_replace_recursive( $config, $entries[ $environment ] );
        $config = array_diff_recursive( $config ); // Remove empty entries.

        $entries = array_diff_recursive( $entries ); // Remove empty entries.
        $content = formatConfig( $entries );
        safeWrite( $configPath, $content );

    }
?>
