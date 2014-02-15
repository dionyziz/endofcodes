<?php
    function migrate( $sql_array = [] ) {
        require_once '../../config/config-local.php';
        require_once '../../models/database.php';
        require_once '../../models/db.php';

        global $config;

        $config = getConfig()[ getEnv( 'ENVIRONMENT' ) ];
        dbInit();

        foreach ( $sql_array as $sql ) {
            try {
                $res = db( $sql );
            }
            catch ( DBException $e ) {
                die( "Migration failed. SQL query died with the following error: " . mysql_error() . "\n" );
            }
        }
        echo "Migration successful.\n";
    }
?>
