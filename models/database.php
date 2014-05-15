<?php
    function dbInit() {
        global $config;

        $dbConnected = mysql_connect( $config[ 'db' ][ 'host' ], 
        	           $config[ 'db' ][ 'user' ], 
        	           $config[ 'db' ][ 'pass' ] );
       	if ( !$dbConnected ) { 
        	throw new DBException( 'Failed to connect to MySQL.', mysql_error() );
    	}
        $dbSelected = mysql_select_db( $config[ 'db' ][ 'dbname' ] );
        if ( !$dbSelected ) { 
        	throw new DBException( 'Failed to select MySQL database.', mysql_error() );
    	}
    } 
    function loadConfig( $environment = '' ) {
    	$path = 'config/config-local.php';
    	$config = array();
		if ( file_exists( $path ) ) {
            $config = require $path;
        }
        if ( !empty($environment) ) {
        	$config = $config[ $environment ];
        	$config = $config[ 'db' ];
        }
        return $config;
    }
    function createConfig( $entries, $environment ) {
    	$entries = ['db' => $entries];
    	$entries = [$environment => $entries];

    	$path = 'config/config-local.php';
    	$config = loadConfig();
        $config = array_replace_recursive( $config, $entries );

    	$content = '<?php' . PHP_EOL . 'return ' . var_export($config, true) . ';';

    	if ( !file_exists($path) ) {
    		touch( $path );
    	}
    	if ( is_writable( $path ) ) {
    		$configSaved = is_numeric( file_put_contents( $path, $content ) );
    	}
    	if ( empty( $configSaved ) ) {
    		return $content;
    	}
    	return false; //Success
    }
?>
