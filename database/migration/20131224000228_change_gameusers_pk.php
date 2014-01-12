<?php
    include 'migrate.php';

    migrate(
        array(
            'ALTER TABLE
                gameusers
            DROP INDEX
                uc_gameusers',
            'ALTER TABLE
                gameusers
            ADD CONSTRAINT pk_gameusers PRIMARY KEY (gameid,userid)'
        )
    );
?>
