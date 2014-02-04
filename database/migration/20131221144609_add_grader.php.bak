<?php
    include 'migrate.php';

    migrate(
        [
            'CREATE TABLE IF NOT EXISTS
                games (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    created date NOT NULL,
                    height int(11) NOT NULL,
                    width int(11) NOT NULL,
                    PRIMARY KEY ( id )
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;',
            'CREATE TABLE IF NOT EXISTS
                creatures (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    gameid int(11) NOT NULL,
                    userid int(11) NOT NULL,
                    PRIMARY KEY( id )
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;',
            'CREATE TABLE IF NOT EXISTS
                gameusers (
                    userid int(11) NOT NULL,
                    gameid int(11) NOT NULL,
                    CONSTRAINT uc_gameusers UNIQUE KEY ( userid, gameid )
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;',
            'CREATE TABLE IF NOT EXISTS
                roundcreatures (
                    roundid int(11) NOT NULL,
                    gameid int(11) NOT NULL,
                    creatureid int(11) NOT NULL,
                    desire varchar(6) COLLATE utf8_unicode_ci NOT NULL,
                    locationx int(11),
                    locationy int(11),
                    hp int(3),
                    CONSTRAINT uc_roundcreatures UNIQUE KEY ( roundid, gameid, creatureid )
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;'
        ]
    );
?>
