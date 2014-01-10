<?php
    $form = new Form( 'session', 'delete' );
    $form->id = 'logout-form';
    $form->formMethod = 'post';
    $form->output( function() {
        Form::createInput( 'submit', '', '', 'Logout' );
    } );
?>
