<?php
    require 'views/header.php';

    $migrations = array_combine( $migrations, $migrations );
    $envs = [ 'test', 'development' ];
    $envs = array_combine( $envs, $envs );
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $migrations, $envs ) {
        $self->createSelect( $migrations, 'name' );
        $self->createSelect( $envs, 'env' );
        $self->createSubmit( 'Run migration' );
    } );
    ?><h2>Last Migrations:</h2><?php
    if ( isset( $last ) ) {
        ?><table class="table">
            <tbody>
                <?php
                    foreach ( $last as $key => $migration ) {
                        if ( empty( $migration ) ) {
                            $migration = 'unknown';
                        }
                        ?><tr>
                            <td><?php 
                                echo $key; 
                            ?></td>
                            <td><?php 
                                echo $migration; 
                            ?></td>
                        </tr><?php
                    }
            ?></tbody>
        </table><?php
    }
    else {
        ?><p>You have not created any logs yet.</p><?php
    }
    ?><h2>All Migrations <small>in case of new database</small></h2><?php
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $envs ) {
        $self->createSelect( $envs, 'env' );
        $self->createInput( 'hidden', 'all', 'all', true );
        $self->createSubmit( "Run all migrations" );
    } );
    ?><h2>Pending migrations</h2><?php
    $form = new Form( 'migrationrun', 'create' );
    $form->output( function( $self ) use( $envs ) {
        $self->createSelect( $envs, 'env' );
        $self->createSubmit( "Run pending migrations" );
    } );
    foreach ( $pending as $env => $migs ) {
        ?><h3><?php 
            echo $env; 
        ?></h3>
        <p>Total: <?php 
            echo count( $migrations ); 
        ?> Pending: <?php 
            echo count( $migs ); 
        ?></p>
        <ol><?php
            foreach ( $migs as $mig ) {
                ?><li><?php 
                    echo $mig; 
                ?></li><?php
            }
        ?></ol><?php
    }

    require 'views/footer.php';
?>
