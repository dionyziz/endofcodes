<!DOCTYPE html>

<html>
    <head>
        <title>EndofCodes Demo</title>
        <?php
            function includeStyle( $path ) {
                ?><link
                    rel="stylesheet"
                    type="text/css"
                    href="<?php
                        echo "static/style/" . $path . ".css";
                    ?>" /><?php
            }
            includeStyle( "general" );
            includeStyle( "header" );
            includeStyle( "home" );
        ?>
        <link 
            rel="stylesheet" 
            type="text/css" 
            href="static/style/header.css" />
        <meta 
            http-equiv="Content-type" 
            content="text/html; charset=utf-8" />
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
                        ?><select>
                            <option> </option>
                            <option value="logout">Logout</option>
                        </select></li><?php
                    }
                    else {
                        ?><li id="login"><a href="index.php?resource=session&amp;method=create">Login</a></li><?php
                    }
                ?>
            </ul>
        </div>
