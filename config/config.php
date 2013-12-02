<?php
    function getConfig() {
        $config = array(
            'db' => array(
                'host' => 'localhost',
                'user' => 'akelas',
                'pass' => 'akelas',
                'dbname' => 'endofcodes'
            ),
            'files' => array(
                'avatar_extentions' => array( 'jpg', 'png', 'jpeg' )
            ),
            'paths' => array(
                'avatar_path' => 'sample_path'
            )
        );
        return $config;
    }
?>
