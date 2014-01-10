<?php
    include 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                roundcreatures
            DROP PRIMARY KEY',
            'ALTER TABLE
                roundcreatures
            ADD CONSTRAINT pk_roundcreatures PRIMARY KEY (gameid,roundid,creatureid)'
        )
    );
?>
