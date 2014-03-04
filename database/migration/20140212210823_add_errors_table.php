<?php
    require_once 'migrate.php';

    Migration::createTable(
        'errors',
        [
            'gameid' => 'int(11) NOT NULL',
            'userid' => 'int(11) NOT NULL',
            'error' => 'varchar(20) COLLATE utf8_unicode_ci NOT NULL'
        ],
        [ 
            ['type' => 'primary', 'field' => [ 'gameid', 'userid', 'error' ], 'name' => 'pk_errors' ]
        ]
    );
?>
