<?php
    include 'migrate.php';

    migrate(
        [
            'ALTER TABLE  
                `users` 
            CHANGE  
                `forgotpasswordexptime`  `forgotpasswordrequestcreated` DATETIME NULL DEFAULT NULL;'
        ]
    );
?>
