<?php
    require 'views/header.php';
?>

<div class="text-center"><?php
    $form = new Form( 'session', 'create' );
    $form->id = 'login-form';
    $form->output( function( $self ) use ( $usernameEmpty, $passwordEmpty,
            $usernameWrong, $passwordWrong ) {
        if ( isset( $usernameEmpty ) ) {
            $self->createError( "Please type a username." );
        }
        if ( isset( $passwordEmpty ) ) {
            $self->createError( "Please type a password." );
        }
        $self->createLabel( 'username', 'Username' );
        if ( isset( $usernameWrong ) ) {
            $self->createError( "Username doesn't exist." );
        }
        $self->createInput( 'text', 'username', 'username' );
        $self->createLabel( 'password', 'Password' );
        if ( isset( $passwordWrong ) ) {
            $self->createError( "Password is incorrect." );
        }
        $self->createInput( 'password', 'password', 'password' );
        $self->createLabel( 'persistent', 'Remember me' );
        $self->createInput( 'checkbox', 'persistent', 'persistent', '', [
            'checked' => true
        ] );
        ?><p><a href="forgotpasswordrequest/create">Forgot password?</a></p><?php
        $self->createInput( 'submit', '', '', 'Login' );
    } );
    ?><p><a href="user/create">Don't have an account?</a></p>
</div>

<?php
    require 'views/footer.php';
?>
