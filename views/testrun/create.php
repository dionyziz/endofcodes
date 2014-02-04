<?php
    include 'views/header.php';

    ?><h1>Select a test to run</h1><?php

    foreach ( $tests as $test ) {
        $form = new Form( 'testrun', 'create' );
        $form->output( function( $self ) use ( $test ) {
            $self->createInput( 'hidden', 'name', '', $test );
            $self->createInput( 'submit', '', '', $test );
        } );
    }
    include 'views/footer.php';
?>
