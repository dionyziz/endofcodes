<?php
    $form = new Form( 'session', 'delete' );
    $form->id = 'logout-form';
    $form->output( function( $self ) {
        $self->createInput( 'submit', '', '', 'Logout' );
    } );
?>
