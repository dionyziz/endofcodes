<?php
    require_once 'migrate.php';

    migrate(
        [
            'CREATE TABLE IF NOT EXISTS
                `users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                    `password` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
                    `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
                    `salt` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
                    `avatarid` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `username` (`username`),
                    UNIQUE KEY `email` (`email`)
                )
                ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;',
            'CREATE TABLE IF NOT EXISTS
                `images` (
                    `imageid` int(11) NOT NULL AUTO_INCREMENT,
                    `userid` int(11) NOT NULL,
                    `imagename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
                    PRIMARY KEY (`imageid`)
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;'
        ]
    );
?>
