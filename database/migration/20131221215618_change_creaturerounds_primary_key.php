<?php
    require 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                roundcreatures
            DROP INDEX
                uc_roundcreatures',
            'ALTER TABLE
                roundcreatures
            ADD CONSTRAINT pk_roundcreature PRIMARY KEY (gameid,roundid,creatureid)'
        ]
    );
?>
