<?php
    require 'views/header.php';
?>

<h1 class='text-center' id='title'>Bot Comparison<h1>
<p id='botcomparison-desc' class='alert alert-info'>Welcome to the bot comparison feature. Here you can compare two bots to check which one is better.</p>
<?php
    if ( $bot_fail ) {
        switch ( $whichBot ) {
            case '1':
                $which = 'first';
                break;
            case '2':
                $which = 'second';
                break;
        }
        ?><p class='alert alert-danger'>Your <?= $which ?> bot is incorrectly configured <img src='static/images/cancel.png' alt='cross' /></p><?php
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
        ?><p class='error text-center'><?php
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
    $form = new Form( 'botcomparison', 'create' );
    $form->id = 'botcomparison-form';
    $form->output( function( $self ) use( $emptyUrl ) {
        global $config;

        ?><div class="form-group"><?php
            if ( isset( $emptyUrl ) ) {
                $self->createError( 'Both urls must not be empty' );
            }
            if ( isset( $username_used ) ) {
                $self->createError( 'Username already exists' );
                $username_value = "";
            }
            else {
                $username_value = "";
            }
            $self->createInput( 'text', 'url1', 'url1', '', [
                'class' => 'form-control',
                'placeholder' => 'First Bot'
            ] );
            $self->createInput( 'text', 'url2', 'url2', '', [
                'class' => 'form-control',
                'placeholder' => 'Second Bot'
            ] );
        ?></div>
        <div class="form-group text-center"><?php
            $self->createSubmit( 'Run Battle', [ 'class' => 'btn btn-primary' ] );
        ?></div><?php
    } ); ?>

<?php
    require 'views/footer/view.php';
?>
