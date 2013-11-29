<!DOCTYPE html>

<html>
    <head>
        <title>EndofCodes Demo</title>
    </head>
    <body>
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
        </ul>
        <?php
            if ( isset( $_SESSION[ 'user' ][ 'username' ] ) ) {
                ?><p><?php
                    echo htmlspecialchars( $_SESSION[ 'user' ][ 'username' ] );
                ?></p>
                <select>
                    <option></option>
                    <option value="logout">Logout</option>
                </select><?php
            }
            else {
                ?><p><a href="index.php?resource=session&amp;method=create">Login</a></p><?php
            }
        ?>
