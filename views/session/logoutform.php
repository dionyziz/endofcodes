<?php
    $form = new Form( 'session', 'delete' );
    $form->id = 'logout-form';
    $form->output( function() {
        Form::createInput( 'submit', '', '', 'Logout' );
    } );
?>
