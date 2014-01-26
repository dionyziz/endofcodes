<?php
    function getConfig() {
        $config = array(
            'development' => array(
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
                'persistent_cookie' => array(
                    'name' => 'sessionid',
                    'duration' => 60 * 60 * 24 * 365 * 20, 
                    'unset_time' => 60 * 60 * 24 * 2
                )
            ),
            'test' => array(
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
                'persistent_cookie' => array(
                    'name' => 'sessionid',
                    'duration' => 60 * 60 * 24 * 365 * 20, 
                    'unset_time' => 60 * 60 * 24 * 2
                )
            )
        );
        return $config;
    }
?>
