<?php
    require 'migrate.php';

    Migration::createTable( 
        'follows',
        [
            'followerid' => 'int(11) NOT NULL',
            'followedid' => 'int(11) NOT NULL'
        ],
        [
            [ 'type' => 'primary', 'field' => [ 'followerid', 'followedid' ], 'name' => 'pk_follows' ]
        ]
    );
?>
