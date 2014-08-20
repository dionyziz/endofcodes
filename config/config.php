<?php
    return [
        'defaults' => [
            'db' => [
                'host' => 'localhost',
                'user' => 'endofcodes',
                'pass' => 'sample_pass',
                'dbname' => 'endofcodes'
            ],
            'files' => [
                'avatar_extentions' => [ 'jpg', 'png', 'jpeg' ],
                'flag_extention' => 'png'
            ],
            'paths' => [
                'avatar_path' => 'avatars/',
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
            'forgot_password_exp_time' => 3600 * 24,
            'cli_max_width' => 120,
            'email' => 'team@endofcodes.com'
        ],
        'development' => [
            'db' => [
                'user' => 'endofcodes',
                'pass' => 'sample_pass', //override in config-local.php
                'dbname' => 'endofcodes'
            ],

        ],
        'test' => [
            'db' => [
                'user' => 'travis',
                'pass' => '',
                'dbname' => 'endofcodes_test'
            ],
        ]
    ];
?>
