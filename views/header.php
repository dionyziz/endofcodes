<!DOCTYPE html>

<html lang="en">
    <head>
        <title>EndofCodes Demo</title>
        <?php
            includeStyle( "general" );
            includeStyle( "header" );
            includeStyle( "home" );
            includeStyle( "register" );
            includeStyle( "login" );
        ?>
        <meta charset="utf-8" /> 
    </head>
    <body>
        <div id="header">
            <ul>
                <li><a href="index.php">Endofcodes</a></li>
                <li><a href="">Rules</a></li>
                <?php
                    if ( isset( $_SESSION[ 'user' ]['username' ] ) ) {
                        ?><li><a href="index.php?resource=user&amp;method=view&amp;username=<?php
                            echo htmlspecialchars( $_SESSION[ 'user' ][ 'username' ] );
                        ?>">Profile</a></li><?php
                    }
                ?>
                <li><a href="http://blog.endofcodes.com">Blog</a></li>
                <?php
                    if ( isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                        ?><li id="login" class="username"><?php
                            echo htmlspecialchars( $_SESSION[ 'user' ][ 'username' ] );
                        ?></li><?php
                    }
                    else {
                        ?><li id="login"><a href="index.php?resource=session&amp;method=create">Login</a></li><?php
                    }
                ?>
            </ul>
        </div>
