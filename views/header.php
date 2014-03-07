<!DOCTYPE html>

<html lang="en">
    <head>
        <title>EndofCodes Demo</title>
        <base href='<?php
        global $config;

        echo $config[ 'base' ];
        ?>' />
        <?php
            includeStyle( "general" );
            includeStyle( "header" );
            includeStyle( "footer" );
            includeStyle( "navigation" );
            includeStyle( "home" );
            includeStyle( "register" );
            includeStyle( "login" );
            includeStyle( "test" );
            includeStyle( "links" );
        ?>
        <script type="text/javascript" src="../script/cssrefresh.js"></script>

        <meta charset="utf-8" />
    </head>
    <body>
        <div id="header">
            <ul>
                <?php
                    if ( isset( $_SESSION[ 'user' ] ) ) {
                        ?><li id="login" class="username"><?php
                            echo htmlspecialchars( $_SESSION[ 'user' ]->username );
                        ?></li><?php
                    }
                    else {
                        ?><li id="login"><a href="session/create">Login</a> or <a href="user/create">Register</a></li><?php
                    }
                ?>
                <li><h1><a href="index.php">End of Codes</a></h1></li>
                <?php
                    if ( isset( $_SESSION[ 'user' ] ) ) {
                        ?><li><a href="index.php?resource=user&amp;method=view&amp;username=<?php
                            echo htmlspecialchars( $_SESSION[ 'user' ]->username );
                        ?>">Profile</a></li><?php
                    }
                    if ( isset( $user ) ) {
                        require_once 'views/session/logoutform.php';
                    }
                ?>
            </ul>
        </div>
        <div class='content'>
