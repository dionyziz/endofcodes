<?php
    include 'migrate.php';

    Migration::createTable( 
        'follows',
        [
            'followerid' => 'int(11) NOT NULL',
            'followedid' => 'int(11) NOT NULL'
        ]
    );
    Migration::addPrimaryKey( 'follows', 'pk_follows', [ 'followerid', 'followedid' ] );
?>
