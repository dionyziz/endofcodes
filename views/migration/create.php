<?php
    require 'views/header.php';

    foreach ( $migrations as $migration ) {
        $list[] = [
            'content' => $migration,
            'value' => $migration
        ];
    }
    $form = new Form( 'migration', 'create' );
    $form->output( function( $self ) use( $list ) {

        $self->createSelect( 'name', '', $list );
        $self->createSelect( 'env', 'env', [
            [
                'content' => 'development',
                'value' => 'development'
            ],
            [
                'content' => 'test',
                'value' => 'test'
            ]
        ] );
        $self->createSubmit( 'Run migration' );
    } );
    ?><p> Last Migration: <?php echo $last; ?></p><?php

    $form = new Form( 'migration', 'create' );
    $form->output( function( $self ) use( $list ) {
        $self->createSelect( 'env', 'env', [
            [
                'content' => 'development',
                'value' => 'development'
            ],
            [
                'content' => 'test',
                'value' => 'test'
            ]
        ] );
        $self->createSubmit( "Run Migrations that haven't been executed" );
    } );

    require 'views/footer.php';
?>
