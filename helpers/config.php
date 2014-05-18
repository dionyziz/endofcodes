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

        $content = preg_replace( '/\s*array \(/', ' [', $content );
        $content = str_replace( ')', ']', $content );
        $content = str_replace( '  ', '    ', $content );
        // Todo: Indent code inside <?php tags.
        
        return $content;
    }
    function updateConfig( $entries, $environment ) {
        $entries = [ $environment => $entries ];

        $content = formatConfig( $entries );
        $path = 'config/config-local.php';
        $touched = @touch( $path );
        if ( $touched ) {
            $configSaved = file_put_contents( $path, $content ) !== false;
        }
        if ( empty( $configSaved ) ) {
            throw new FileNotWritableException( $path, $content );
        }
        global $config;
        $config = array_replace_recursive( $config, $entries );
    }
?>
