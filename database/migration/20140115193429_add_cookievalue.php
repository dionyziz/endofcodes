<?php
    include_once 'migrate.php';

    migrate(
        [
            "ALTER TABLE
                `users`
            ADD
                `cookievalue` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;"
        ]
    );
?>
