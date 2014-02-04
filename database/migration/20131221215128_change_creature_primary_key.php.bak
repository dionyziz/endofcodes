<?php
    include 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                creatures
            DROP PRIMARY KEY',
            'ALTER TABLE
                creatures
            ADD CONSTRAINT pk_creatures PRIMARY KEY (gameid,userid,id)'
        ]
    );
?>
