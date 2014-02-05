<?php
    function getConfig() {
        $config = [
            'development' => [
                'db' => [
                    'host' => 'sample_host',
                    'user' => 'sample_user',
                    'pass' => 'sample_pass',
                    'dbname' => 'sample_dbname'
                ],
                'files' => [
                    'avatar_extentions' => [ 'jpg', 'png', 'jpeg' ],
                    'flag_extention' => 'png'
                ],
                'paths' => [
                    'avatar_path' => 'sample_path',
                    'flag_path' => 'static/images/flags/'
                ],
                'pass_min_len' => 7,
                'age' => [
                    'min' => 8,
                    'max' => 100
                ],
                'persistent_cookie' => [
                    'name' => 'sessionid',
                    'duration' => 60 * 60 * 24 * 365 * 20,
                    'unset_time' => 60 * 60 * 24 * 2
                ],
                'base' => 'http://localhost/endofcodes/',
                'root' => '/var/www/endofcodes/'
            ],
            'test' => [
                'db' => [
                    'host' => 'sample_host',
                    'user' => 'sample_user',
                    'pass' => 'sample_pass',
                    'dbname' => 'sample_dbname'
                ],
                'files' => [
                    'avatar_extentions' => [ 'jpg', 'png', 'jpeg' ],
                    'flag_extention' => 'png'
                ],
                'paths' => [
                    'avatar_path' => 'sample_path',
                    'flag_path' => 'static/images/flags/'
                ],
                'pass_min_len' => 7,
                'age' => [
                    'min' => 8,
                    'max' => 100
                ],
                'persistent_cookie' => [
                    'name' => 'sessionid',
                    'duration' => 60 * 60 * 24 * 365 * 20,
                    'unset_time' => 60 * 60 * 24 * 2
                ],
                'base' => 'http://localhost/endofcodes/',
                'root' => '/var/www/endofcodes/'
            ]
        ];
        return $config;
    }
?>
