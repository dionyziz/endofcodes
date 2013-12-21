<?php
    include 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                creatures
            DROP PRIMARY KEY',
            'ALTER TABLE
                creatures
            ADD CONSTRAINT pk_creature PRIMARY KEY (gameid,userid,id)'
        )
    );
?>
