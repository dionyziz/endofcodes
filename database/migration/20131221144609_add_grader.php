<?php
    include 'migrate.php';

    Migration::createTable(
        'games',
        [ 
            'id' => 'int(11) NOT NULL AUTO_INCREMENT', 
            'created' => 'date NOT NULL',
            'height' => 'int(11) NOT NULL',
            'width' => 'int(11) NOT NULL' 
        ],
        [ 
            'primary' => ['id']
        ]
    );

    Migration::createTable(
        'creatures',
        [ 
            'id' => 'int(11) NOT NULL AUTO_INCREMENT', 
            'gameid' => 'int(11) NOT NULL',
            'userid' => 'int(11) NOT NULL',
        ],
        [ 
            'primary' => ['id']
        ]
    );

    Migration::createTable(
        'gameusers',
        [ 
            'id' => 'int(11) NOT NULL AUTO_INCREMENT', 
            'userid' => 'int(11) NOT NULL',
            'gameid' => 'int(11) NOT NULL',
        ],
        [ 
            'unique' => [ 'userid', 'gameid' ]
        ]
    );

    Migration::createTable(
        'roundcreatures',
        [ 
            'roundid' => 'int(11) NOT NULL', 
            'gameid' => 'int(11) NOT NULL',
            'creatureid' => 'int(11) NOT NULL',
            'desire' => 'varchar(6) COLLATE utf8_unicode_ci NOT NULL', 
            'locationx' => 'int(11)',
            'locationy' => 'int(11)',
            'hp' => 'int(3)',
            'locationy' => 'int(11)',
        ],
        [ 
            'unique' => [ 'roundid', 'gameid', 'creatureid' ]
        ]
    );
?>
