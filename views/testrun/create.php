<?php
    require 'views/header.php';

    ?><h1>Select a test to run</h1><?php

    $form = new Form( 'testrun', 'create' );
    $form->output( function( $self ) {
        $self->createSubmit( 'Run all tests' );
    } );

    foreach ( $tests as $test ) {
        $form = new Form( 'testrun', 'create' );
        $form->output( function( $self ) use ( $test ) {
            $self->createInput( 'hidden', 'name', '', $test );
            $self->createSubmit( $test );
        } );
    }

    require 'views/footer/view.php';
?>
