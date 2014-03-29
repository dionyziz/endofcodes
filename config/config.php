<?php
    return [
        'development' => [
            'db' => [
                'host' => 'localhost',
                'user' => 'endofcodes',
                'pass' => 'sample_pass',
                'dbname' => 'endofcodes'
            ],
            'files' => [
                'avatarExtentions' => [ 'jpg', 'png', 'jpeg' ],
                'flagExtention' => 'png'
            ],
            'paths' => [
                'avatar_path' => 'avatars/',
                'flagPath' => 'static/images/flags/'
            ],
            'passMinLen' => 7,
            'age' => [
                'min' => 8,
                'max' => 100
            ],
            'persistentCookie' => [
                'name' => 'sessionid',
                'duration' => 60 * 60 * 24 * 365 * 20,
                'unsetTime' => 60 * 60 * 24 * 2
            ],
            'forgotPasswordExpTime' => 3600 * 24,
            'cliMaxWidth' => 120,
            'email' => 'team@endofcodes.com',
            'base' => 'http://localhost/endofcodes/'
        ],
        'test' => [
            'db' => [
                'host' => 'localhost',
                'user' => 'travis',
                'pass' => '',
                'dbname' => 'endofcodes_test'
            ],
            'files' => [
                'avatarExtentions' => [ 'jpg', 'png', 'jpeg' ],
                'flagExtention' => 'png'
            ],
            'paths' => [
                'avatarPath' => 'avatars/',
                'flagPath' => 'static/images/flags/'
            ],
            'passMinLen' => 7,
            'age' => [
                'min' => 8,
                'max' => 100
            ],
            'persistentCookie' => [
                'name' => 'sessionid',
                'duration' => 60 * 60 * 24 * 365 * 20,
                'unsetTime' => 60 * 60 * 24 * 2
            ],
            'forgotPasswordExpTime' => 3600 * 24,
            'cliMaxWidth' => 120,
            'email' => 'team@endofcodes.com',
            'base' => 'http://localhost/endofcodes/'
        ]
    ];
?>
