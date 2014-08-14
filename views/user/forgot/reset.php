<?php
    include 'views/header.php';
?> 
<h1 id='pass-reset-title' class='text-center'>Please enter a new password</h1>
<div>
<?php 
    $form = new Form( 'forgotpasswordrequest', 'update' );  
    $form->id = 'password-reset';
    $form->output( function( $self ) use ( $password_empty, $password_invalid, $password_not_matched, $password_token ) {
        if ( $password_empty ) {
            $self->createError( "Please enter a new password" );
        }
        if ( $password_invalid ) {
            $self->createError( "Your new password must be more than 6 characters long" );
        }
        if ( $password_not_matched ) {
            $self->createError( "Your two passwords do not match" );
        }
        ?><div class="form-group"><?php
            $self->createInput( 'text', 'password', '', '' , [
                'class' => 'form-control',
                'placeholder' => 'Password',
                'autofocus' => '1'
            ] );
        ?></div><?php
        ?><div class="form-group"><?php
            $self->createInput( 'text', 'password_repeat', '', '' , [
                'class' => 'form-control',
                'placeholder' => 'Repeat',
                'autofocus' => '1'
            ] );
        ?></div><?php
        $self->createInput( 'hidden', 'password_token', '', $password_token );
        ?><div class="form-group"><?php
            $self->createInput( 'submit', '', '', 'Reset your password', [
                'class' => 'btn btn-primary'
            ] );
        ?></div><?php
    } );
?>
</div><?
    include 'views/footer/view.php';
?>
