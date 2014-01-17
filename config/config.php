<?php
    function getConfig() {
        $config = array(
            'db' => array(
                'host' => 'sample_host',
                'user' => 'sample_user',
                'pass' => 'sample_pass',
                'dbname' => 'sample_dbname'
            ),
            'files' => array(
                'avatar_extentions' => array( 'jpg', 'png', 'jpeg' ),
                'flag_extention' => 'png' 
            ),
            'paths' => array(
                'avatar_path' => 'sample_path',
                'flag_path' => 'static/images/flags/'
            ),
            'pass_min_len' => 7,
            'age' => array(
                'min' => 8,
                'max' => 100
            ),
            'persistent_cookie' => array (
                'name' = 'sessionid',
                'duration' => '3600 * 24 * 365', 
                'unset_time' =>  '3600 * 24'
            )
        );
        if ( getEnv( 'DB_USER' ) !== false ) {
            $config[ 'db' ][ 'user' ] = getEnv( 'DB_USER' );
        }
        if ( getEnv( 'DB_PASSWORD' ) !== false ) {
            $config[ 'db' ][ 'pass' ] = getEnv( 'DB_PASSWORD' );
        }
        return $config;
    }
?>
