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
        return $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $relativePath;
    }
    function calculateConfigEntries( &$config ) {
        $config[ 'root' ] = getcwd();
        if ( isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
            $config[ 'base' ] = getBase();
        }
    }
    function validateConfigEntries( $config ) {
        if ( $config[ 'development' ][ 'db' ][ 'dbname' ] == $config[ 'test' ][ 'db' ][ 'dbname' ] ) {
            throw new ModelValidationException( " Database name for development and testing is the same." );
        }
    }
    function loadConfig( $environment ) {
        $config = require 'config/config.php';
        if ( file_exists( 'config/config-local.php' ) ) {
            $configLocal = require 'config/config-local.php';
            $config = array_replace_recursive( $config, $configLocal );
        }
        validateConfigEntries( $config );
        $config = array_replace_recursive( $config[ 'defaults' ], $config[ $environment ] );

        calculateConfigEntries( $config );
        return $config;
    }
    function formatConfig( $config ) {
        $content = '<?php' . "\n"
                 . 'return ' . var_export( $config, true ) . ';' . "\n"
                 . '?>' . "\n" ;

        // Convert 2-space indentation to 4-space.
        $content = str_replace( '  ', '    ', $content );
        // Replace array(...) with [...].
        $content = preg_replace( '/\s*array \(/', ' [', $content );
        $content = str_replace( ')', ']', $content );
        // Indent code inside <?php tags.
        $content = preg_replace( '/(^[^(<)|(\?)])/m', '    $1', $content );

        return $content;
    }
    // Updates $config and the config-local.php file to include the given $entries.
    // Can also be used to delete $entries that are set to NULL.
    function updateConfig( $entries, $environment ) {
        global $config;

        $config = array_replace_recursive( $config, $entries );
        $config = array_diff_recursive( $config ); // Remove NULL entries.

        $entries = [ $environment => $entries ];
        $configPath = 'config/config-local.php';

        if ( file_exists( $configPath ) ) {
            $localEntries = include $configPath;
            $entries = array_replace_recursive( $localEntries, $entries );
        }
        $entries = array_diff_recursive( $entries ); // Remove NULL entries.
        $content = formatConfig( $entries );
        safeWrite( $configPath, $content );
    }
?>
