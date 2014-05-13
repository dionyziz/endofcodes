<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>EndofCodes Demo</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel='icon' type='image/png' href='static/images/gamepad.png' />
        <base href='<?php
        global $config;

        echo $config[ 'base' ];
        ?>' />
        <?php
            includeStyle( "bootstrap.min" );
            includeStyle( "general" );
            includeStyle( "footer" );
            includeStyle( "navigation" );
            includeStyle( "home" );
            includeStyle( "user/create" );
            includeStyle( "session/create" );
            includeStyle( "test" );
            includeStyle( "links" );
            includeStyle( "game/view" );
            includeStyle( "user/view" );
        ?>
        <script type="text/javascript" src="static/script/jquery-2.1.0.min.js"></script>
        <script type="text/javascript" src="static/script/bootstrap.min.js"></script>
        <script type="text/javascript" src="static/script/logout.js"></script>
        <script type="text/javascript" src="static/script/game/view.js"></script>
        <script type="text/javascript" src="static/script/prefixfree.min.js"></script>
        <script type="text/javascript" src="static/script/form_validation.js"></script>
        <script type="text/javascript" src="static/script/user/view.js"></script>
        <meta charset="utf-8" />
    </head>
    <body>
        <div class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="">End Of Codes</a>
                </div>
                <ul class="nav navbar-nav navbar-left">
                    <li class="active"><a href="">Home</a></li>
                    <li><a href="">Rules</a></li>
                    <li><a href="http://blog.endofcodes.com">Blog</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php
                        if ( isset( $_SESSION[ 'user' ] ) ) {
                            $user = $_SESSION[ 'user' ];
                            ?><li><a href="user/view?username=<?php
                                echo $user->username;
                            ?>"><img src="<?php
                                echo $user->image->target_path;
                            ?>" /><?php
                                echo $user->username;
                            ?></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span class="settings-icon glyphicon glyphicon-cog"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li role="presentation">
                                        <a href="user/update">Settings</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#" id="logout">Sign out</a>
                                    </li>
                                    <?php
                                        $form = new Form( 'session', 'delete' );
                                        $form->id = 'logout-form';
                                        $form->output();
                                    ?>
                                </ul>
                            </li><?php
                        }
                        else {
                            ?><li><a href="session/create">Login</a></li>
                            <li class="active"><a href="user/create">Register</a></li><?php
                        }
                    ?>
                </ul>
            </div>
        </div>
        <div class="container" id="main">
