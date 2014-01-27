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
                'base' => 'http://localhost/endofcodes/'
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
                'base' => 'http://localhost/endofcodes/'
            )
        );
        return $config;
    }
?>
