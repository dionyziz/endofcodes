<?php
    include 'views/header.php';
?> 
<p>Please enter a new password</p>
<div>
<?php 
    $form = new Form( 'forgotpasswordrequest', 'update' );  
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
        $self->createLabel( 'password', 'Password' );
        $self->createInput( 'password', 'password' );
        $self->createLabel( 'password_repeat', 'Password (repeat)' );
        $self->createInput( 'password', 'password_repeat' );
        $self->createInput( 'submit', '', '', 'Change password' );
        $self->createInput( 'hidden', 'password_token', '', $password_token );
    } );
?>
</div><?
    include 'views/footer/view.php';
?>
