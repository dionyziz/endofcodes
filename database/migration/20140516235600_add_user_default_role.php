<?php
    Migration::migrate( "UPDATE users SET role = 0 WHERE role IS NULL" );
    Migration::migrate( "ALTER TABLE users ALTER role SET DEFAULT '0'" );
?>
