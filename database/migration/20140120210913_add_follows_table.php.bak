<?php
    include 'migrate.php';

    migrate(
        [
            'CREATE TABLE IF NOT EXISTS
                follows (
                    followerid int(11) NOT NULL,
                    followedid int(11) NOT NULL,
                    CONSTRAINT pk_follows PRIMARY KEY ( followerid, followedid )
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;'
        ]
    );
?>
