<?php
    require 'migrate.php';

    migrate(
        [
            'CREATE TABLE IF NOT EXISTS
                `errors` (
                    `gameid` int(11) NOT NULL,
                    `userid` int(11) NOT NULL,
                    `error` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
                    CONSTRAINT pk_errors PRIMARY KEY (gameid,userid,error)
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;'
        ]
    );
?>
