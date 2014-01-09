<?php
    $form = new Form( 'session', 'delete' );
    $form->id = 'logout-form';
    $form->token = $token;
    $form->output( function() {
        Form::createInput( 'submit', '', '', 'Logout' );
    } );
?>
