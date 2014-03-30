<?php
    require 'views/header.php';
?>

<div class="text-center">
    <p>To begin playing, you must set up your bot.<a href=''> Start by reading the tutorial.</a></p><?php
    if ( !$botFail ) {
        ?><p class='check'>Your bot is correctly configured <img src='static/images/check.png' alt='check' /></p><?php
    }
    else if ( $botFail ) {
        ?><p class='alert alert-danger'>Your bot is incorrectly configured <img src='static/images/cancel.png' alt='cross' /></p><?php
        $errors = [
            'initiateCouldNotResolve' => 'Your bot hostname is invalid. Did you enter a valid hostname?',
            'initiateCouldNotConnect' => 'Your bot is unreachable on the network. Did you enter your public IP address?',
            'initiateHttpCodeNotok' => 'Your bot is running, but responded with an invalid HTTP code. Did you write code to handle initiation?',
            'initiateInvalidJson' => 'Your bot is not sending valid JSON. Did you write code to generate JSON correctly?',
            'initiateInvalidJsonDictionary' => 'You must set the bot name, version, and your username. Did you build the correct JSON dictionary?',
            'initiateUsernameMismatch' => 'Your bot is not using your username. Did you set your username correctly?'
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
    $form->output( function( $self ) use( $boturlEmpty, $boturlInvalid ) {
        $self->createLabel( 'boturl', 'Bot URL' );
        if ( $boturlEmpty ) {
            $self->createError( 'Please enter your bot URL' );
        }
        if ( $boturlInvalid ) {
            $self->createError( 'Please enter a valid HTTP URL' );
        }
        $self->createInput( 'text', 'boturl', 'boturl', $_SESSION[ 'user' ]->boturl );
        $self->createInput( 'submit', '', '', 'Save bot settings' );
    } );
?></div>

<?php
    require 'views/footer.php';
?>
