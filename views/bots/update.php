<?php
    require 'views/header.php';?>

    <p>To begin playing, you must set up your bot.<a href=''> Start by reading the tutorial.</a></p><?php
    if ( $bot_success ) {
        ?><p class='error'>Your bot is correctly configured <img src='static/images/check.png' /></p><?php
    }
    elseif ( $bot_not_success ) {
        ?><p class='error'>Your bot is incorrectly configured <img src='static/images/cancel.png' /></p><?php
        switch ( $error ) {
            case 'could_not_resolve':
                ?><p class='error'>Your bot hostname is invalid. Did you enter a valid hostname?</p><?php
                break;
            case 'could_not_connect':
                ?><p class='error'>Your bot is unreachable on the network. Did you enter your public IP address?</p><?php
                break;
            case 'http_code_not_ok':
                ?><p class='error'>Your bot is running, but not responding to initiation. Did you write code to handle initiation?</p><?php
                break;
            case 'invalid_json':
                ?><p class='error'>Your bot is not sending valid JSON. Did you write code to generate JSON correctly?</p><?php
                break;
            case 'invalid_json_dictionary':
                ?><p class='error'>You must set the bot name, version, and your username. Did you build the correct JSON dictionary?</p><?php
                break;
            case 'username_mismatch':
                ?><p class='error'>Your bot is not using your username. Did you set your username correctly?</p><?php
                break;
        }
    }
    ?><p>API key: <?php echo $apikey ?></p><?php
    $form = new Form( 'bot', 'update' );
    $form->output( function( $self ) use( $boturl_empty, $boturl_invalid ) {
        $self->createLabel( 'boturl', 'Bot URL' );
        if ( $boturl_empty ) {
            $self->createError( 'Please enter your bot URL' );
        }
        if ( $boturl_invalid ) {
            $self->createError( 'Please enter a valid HTTP URL' );
        }
        $self->createInput( 'text', 'boturl', 'boturl' );
        $self->createInput( 'submit', '', '', 'Save bot settings' );
    } );

    require 'views/footer.php';
?>
