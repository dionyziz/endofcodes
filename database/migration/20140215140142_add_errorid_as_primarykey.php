<?php
    require_once 'migrate.php';

    migrate(
        [
            'ALTER TABLE
                errors
            DROP PRIMARY KEY',
            'ALTER TABLE
                errors
            ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST'
        ]
    );
?>
