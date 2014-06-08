<?php
    require 'views/header.php';
?>

<div id="login">
    <h1 class="text-center" id='title'>Please login</h1><?php
    $form = new Form( 'session', 'create' );
    $form->id = 'login-form';
    $form->output( function( $self ) use ( $username_empty, $password_empty,
            $username_wrong, $password_wrong ) {
        if ( isset( $username_empty ) ) {
            $self->createError( "Please type a username." );
        }
        if ( isset( $password_empty ) ) {
            $self->createError( "Please type a password." );
        }
        if ( isset( $username_wrong ) ) {
            $self->createError( "Username doesn't exist." );
        }
        $self->createInput( 'text', 'username', '', '', [ 'class' => 'form-control input-lg', 'placeholder' => 'Username' ] );
        if ( isset( $password_wrong ) ) {
            $self->createError( "Password is incorrect." );
        }
        $self->createInput( 'password', 'password', '', '', [ 'class' => 'form-control input-lg', 'placeholder' => 'Password' ] );
        ?><p id="check"><?php 
            $self->createInput( 'checkbox', 'persistent', '', '', [ 'checked' => true ] ); 
            ?><span id='remember'>Remember me</span>
            <a id='forgot' href="forgotpasswordrequest/create">Forgot password?</a>
        </p>
        <p><?php 
            $self->createSubmit( 'Login', [ 'class' => 'btn btn-primary' ] ); 
        ?></p><?php
    } );
    ?><!-- <p><span class="round uppercase">OR</span></p>
    <p>
        <p class="google-before rounded"><span class="fontawesome-google-plus"></span></p>
        <button class="google rounded-left btn btn-primary">Login with Google</button>
    </p>
    <p>
        <p class="github-before rounded"><span class="fontawesome-github"></span></p>
        <button class="github rounded-left btn btn-primary">Login with GitHub</button>
    </p>
</div> -->
<p class='text-center'>New to End Of Codes? <a href='user/create'>Create an account</a></p>

<?php
    require 'views/footer/view.php';
?>
