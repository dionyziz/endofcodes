<?php
    Migration::createTable( 
        'users', 
        [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'username' => 'varchar(50) COLLATE utf8_unicode_ci NOT NULL',
            'password' => 'varchar(200) COLLATE utf8_unicode_ci NOT NULL',
            'email' => 'varchar(200) COLLATE utf8_unicode_ci NOT NULL',
            'salt' => 'varchar(200) COLLATE utf8_unicode_ci NOT NULL',
            'avatarid' => 'int(11) NOT NULL',
            'countryid' => 'int(11) NOT NULL'
        ],
        [ 
            [ 'type' => 'primary', 'field' => 'id' ],
            [ 'type' => 'unique', 'field' => 'username', 'name' => 'username' ],
            [ 'type' => 'unique', 'field' => 'email', 'name' => 'email' ]
        ]
    );
    
    Migration::createTable( 
        'images', 
        [
            'imageid' => 'int(11) NOT NULL AUTO_INCREMENT',
            'userid' => 'int(11) NOT NULL',
            'imagename' => 'varchar(200) COLLATE utf8_unicode_ci NOT NULL',
        ],
        [ 
            [ 'type' => 'primary', 'field' => 'imageid' ]
        ]
    );
?>
