<?php
    include 'views/header.php';
?>

<div class='text-center'><?php
    if ( isset( $_SESSION[ 'user' ] ) ) {
        ?><p class="alert alert-warning logged-notice">You are already logged in</p><?php
    }
    ?><h1 id='pass-revoke-title'>Enter your username<small> or email</small><h1><?php
    $form = new Form( 'forgotpasswordrequest', 'create' );
    $form->id = 'password-revoke';
    $form->output( function( $self ) use ( $input_empty, $username_not_exists, $email_not_exists ) {
        if ( $input_empty ) {
            $self->createError( "Please enter your username or email" );
        }
        if ( $email_not_exists ) {
            $self->createError( "This email doesn't exist" );
        }
        if ( $username_not_exists ) {
            $self->createError( "This username doesn't exist" );
        }
        ?><div class="form-group"><?php
            $self->createInput( 'text', 'username', 'username', '' , [
                'class' => 'form-control',
                'placeholder' => 'Username'
            ] );
        ?></div><?php
        ?><div id='submit' class="form-group"><?php
            $self->createInput( 'submit', '', '', 'Reset your password', [
                'class' => 'btn btn-primary'
            ] );
        ?></div><?php
    } );
?>
    <p><a href="session/create">Remembered?</a></p>

</div>

<?php
    include 'views/footer/view.php';
?>
