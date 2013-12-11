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
            'pass_len' => '6'
        );
        return $config;
    }
?>
