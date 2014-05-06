<?php
    require 'views/header.php';
?>

<div class="text-center">
    <p>To begin playing, you must set up your bot.<a href=''> Start by reading the tutorial.</a></p><?php
    if ( !$bot_fail && $user->boturl != '' ) {
        ?><p class='check'>Your bot is correctly configured <img src='static/images/check.png' alt='check' /></p><?php
    }
    else if ( $bot_fail ) {
        ?><p class='alert alert-danger'>Your bot is incorrectly configured <img src='static/images/cancel.png' alt='cross' /></p><?php
        $errors = [
            'initiate_could_not_resolve' => 'Your bot hostname is invalid. Did you enter a valid hostname?',
            'initiate_could_not_connect' => 'Your bot is unreachable on the network. Did you enter your public IP address?',
            'initiate_http_code_not_ok' => 'Your bot is running, but responded with an invalid HTTP code. Did you write code to handle initiation?',
            'initiate_invalid_json' => 'Your bot is not sending valid JSON. Did you write code to generate JSON correctly?',
            'initiate_invalid_json_dictionary' => 'You must set the bot name, version, and your username. Did you build the correct JSON dictionary?',
            'initiate_username_mismatch' => 'Your bot is not using your username. Did you set your username correctly?',
            'initiate_botname_not_set' => 'Your bot is not setting a botname.',
            'initiate_username_not_set' => 'Your bot is not setting a username.',
            'initiate_version_not_set' => 'Your bot is not setting a version.',
            'initiate_additional_data' => 'Your bot is sending more data than expected.'
        ];
        ?><p class='error'><?php
        if ( isset( $errors[ $error->description ] ) ) {
            echo htmlspecialchars( $errors[ $error->description ] );
        }
        else {
            ?>Unknown error<?php
        }
        ?></p><?php
        if ( !empty( $error->actual ) ) {
            ?><p>Your bot sent the following response which was unrecognized:

            <code><?php
            echo htmlspecialchars( $error->actual );
            ?></code></p><?php
        }
        if ( !empty( $error->expected ) ) {
            ?><p>We were expecting the following response instead:

            <code><?php
            echo htmlspecialchars( $error->expected );
            ?></code></p><?php
        }
    }
    $form = new Form( 'bot', 'update' );
    $form->output( function( $self ) use( $boturl_empty, $boturl_invalid, $user ) {
        $self->createLabel( 'boturl', 'Bot URL' );
        if ( $boturl_empty ) {
            $self->createError( 'Please enter your bot URL' );
        }
        if ( $boturl_invalid ) {
            $self->createError( 'Please enter a valid HTTP URL' );
        }
        $self->createInput( 'text', 'boturl', 'boturl', $user->boturl );
        $self->createInput( 'submit', '', '', 'Save bot settings' );
    } );
?></div>

<?php
    require 'views/footer.php';
?>
