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
        if ( !file_exists( $path ) ) {
            return $config;
        }
        $config = require $path;
        if ( !empty( $environment ) ) {
            $config = $config[ $environment ];
            $config = $config[ 'db' ];
        }
        return $config;
    }
    function formatConfig( $config ) {
        $content = '<?php' . PHP_EOL . 'return ' . var_export( $config, true ) . ';' . PHP_EOL . '?>';
        $content = str_replace( '  ', '    ', $content ); //convert 2 spaces to 4
        $content = preg_replace( '/\s*array \(/', ' [', $content );
        $content = str_replace( ')', ']', $content );
        return $content;
    }
    function createConfig( $entries, $environment ) {
        $entries = [ 'db'         => $entries ];
        $entries = [ $environment => $entries ];

        $path = 'config/config-local.php';
        $config = loadConfig();
        $config = array_replace_recursive( $config, $entries );

        $content = formatConfig( $config );

        @touch( $path ); // is_writable will return false if this fails.
        if ( is_writable( $path ) ) {
            $configSaved = is_numeric( file_put_contents( $path, $content ) );
        }
        if ( empty( $configSaved ) ) {
            throw new DBExceptionNotWritable( $content );
        }
    }
    class DBExceptionNotWritable extends DBException {
        public $content;
        public function __construct( $content ) {
            $this->content = $content;
        }
    }
?>
