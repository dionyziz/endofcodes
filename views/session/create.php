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
        $self->createInput( 'text', 'username', '', '', [ 'placeholder' => 'Username' ] );
        if ( isset( $password_wrong ) ) {
            $self->createError( "Password is incorrect." );
        }
        $self->createInput( 'password', 'password', '', '', [ 'placeholder' => 'Password' ] );
        ?><p id="check"><?php $self->createInput( 'checkbox', 'persistent', '', '', [
            'checked' => true
        ] ); ?></p>
        <p>Remember me<a id='forgot' href="forgotpasswordrequest/create">Forgot password?</a></p>
        <p><?php $self->createSubmit( 'LOGIN' ); ?></p><?php
    } );
    ?><p><span class="round">OR</span></p>
    <p>
        <p class="google-before rounded"><span class="fontawesome-google-plus"></span></p>
        <button class="google rounded-left">LOGIN WITH GOOGLE+</button>
    </p>
    <p>
        <p class="github-before rounded"><span class="fontawesome-github"></span></p>
        <button class="github rounded-left">LOGIN WITH GITHUB</button>
    </p>
</div>
<p class='text-center'>New to End Of Codes? <a href='user/create'>Create an account</a></p>

<?php
    require 'views/footer.php';
?>
