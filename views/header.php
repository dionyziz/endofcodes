<!DOCTYPE html>

<html lang="en">
    <head>
        <title>EndofCodes Demo</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
            includeStyle( "bootstrap.min" );
            includeStyle( "links" );
        ?>
        <script type="text/javascript" src="../script/cssrefresh.js"></script>

        <meta charset="utf-8" />
    </head>
    <body>
        <div class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">End Of Codes</a>
                </div>
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="">Rules</a></li>
                    <li><a href="http://blog.endofcodes.com">Blog</a></li><?php
                    if ( isset( $_SESSION[ 'user'] ) ) {
                        ?><li><a 
                            href="index.php?resource=user&amp;method=view&amp;username=
                            <?php
                                echo htmlspecialchars( $_SESSION[ 'user' ]->username );
                            ?> ">Profile</a>
                        </li><?php
                    }
                ?></ul>
                <ul class="nav navbar-nav navbar-right"><?php
                    if ( isset( $_SESSION[ 'user'] ) ) {
                        ?><li><a href="session/delete">Log out<li></a></li><?php
                    }
                    else {
                        ?><li><a href="session/create">Login</a></li><?php
                    }
                    ?><li class="active"><a href="user/create">Register</a></li>
                </ul>
            </div>
        </div>
